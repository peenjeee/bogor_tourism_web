<?php

namespace App\Livewire;

use App\Models\Place;
use App\Services\FlaskApiService;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Cache;

class PlacesList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $search = '';
    public $category = '';
    public $categories = [];
    public $useSemanticSearch = true;

    protected $queryString = [
        'search' => ['except' => ''],
        'category' => ['except' => ''],
    ];

    public function mount()
    {
        // Fixed 7 main categories as per original website
        $this->categories = [
            'Arena',
            'Olahraga',
            'Alam',
            'Seni Budaya',
            'Belanja',
            'Kuliner',
            'Rekreasi'
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    public function setCategory($category)
    {
        $this->category = $category;
        $this->resetPage();
    }

    public function clearSearch()
    {
        $this->search = '';
        $this->resetPage();
    }

    public function render()
    {
        if ($this->search && $this->useSemanticSearch) {
            return $this->renderWithSemanticSearch();
        }

        $query = Place::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('deskripsi', 'like', '%' . $this->search . '%')
                    ->orWhere('alamat', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->category) {
            $query->where('kategori', $this->category);
        }

        $places = $query->orderByDesc('likes')->paginate(12);

        return view('livewire.places-list', [
            'places' => $places,
        ]);
    }

    protected function renderWithSemanticSearch()
    {
        $flaskApi = app(FlaskApiService::class);
        $cacheKey = 'semantic_' . md5($this->search . '_' . $this->category);

        try {
            // Cache results for 10 minutes to survive pagination
            $placeNames = Cache::remember($cacheKey, 600, function () use ($flaskApi) {
                $response = $flaskApi->semanticSearch($this->search);

                // Handle nested API response: {data: {data: [...], status: 'success'}}
                $innerData = $response['data'] ?? [];
                $actualData = $innerData['data'] ?? $innerData; // Support both nested and flat

                if (is_array($actualData) && count($actualData) > 0) {
                    return collect($actualData)->pluck('nama')->toArray();
                }
                return [];
            });

            if (count($placeNames) > 0) {
                // Fuzzy match each name to find in DB
                $orderedPlaces = collect();
                foreach ($placeNames as $name) {
                    // 1. Exact match
                    $place = Place::where('nama', $name)->first();

                    // 2. Fuzzy match (contains)
                    if (!$place) {
                        $place = Place::where('nama', 'LIKE', "%{$name}%")->first();
                    }

                    // 3. Multi-word intersection
                    if (!$place) {
                        $words = explode(' ', $name);
                        if (count($words) > 0) {
                            $q = Place::query();
                            foreach ($words as $word) {
                                $cleanWord = trim($word);
                                if (strlen($cleanWord) > 2) {
                                    $q->where('nama', 'LIKE', "%{$cleanWord}%");
                                }
                            }
                            $place = $q->first();
                        }
                    }

                    if ($place && !$orderedPlaces->contains('id', $place->id)) {
                        // Apply category filter if set
                        if (!$this->category || $place->kategori === $this->category) {
                            $orderedPlaces->push($place);
                        }
                    }
                }

                // Get current page from Livewire's pagination
                $page = $this->getPage();
                $perPage = 12;
                $total = $orderedPlaces->count();
                $items = $orderedPlaces->forPage($page, $perPage);

                $places = new \Illuminate\Pagination\LengthAwarePaginator(
                    $items,
                    $total,
                    $perPage,
                    $page,
                    ['path' => url()->current(), 'pageName' => 'page']
                );

                return view('livewire.places-list', [
                    'places' => $places,
                    'isSemanticSearch' => true,
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Semantic search failed: ' . $e->getMessage());
        }

        // Fallback
        $query = Place::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('deskripsi', 'like', '%' . $this->search . '%')
                    ->orWhere('alamat', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->category) {
            $query->where('kategori', $this->category);
        }

        $places = $query->orderByDesc('likes')->paginate(12);

        return view('livewire.places-list', [
            'places' => $places,
        ]);
    }
}
