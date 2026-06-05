<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Place extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'kategori',
        'label',
        'deskripsi',
        'alamat',
        'latitude',
        'longitude',
        'fasilitas',
        'harga_tiket',
        'jam_operasional',
        'telepon',
        'url',
        'url_gambar',
        'tags',
        'likes',
        'author',
        'sumber'
    ];

    protected $casts = [
        'likes' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    /**
     * Check if place has valid coordinates
     */
    public function hasCoordinates(): bool
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    /**
     * Scope untuk mendapatkan places berdasarkan kategori
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('kategori', $category);
    }

    /**
     * Scope untuk mendapatkan places paling populer
     */
    public function scopePopular($query, $limit = 6)
    {
        return $query->orderBy('likes', 'desc')->limit($limit);
    }

    /**
     * Get short description (first 200 chars)
     */
    public function getShortDescriptionAttribute()
    {
        if (strlen($this->deskripsi) > 150) {
            return substr($this->deskripsi, 0, 150) . '...';
        }
        return $this->deskripsi;
    }

    /**
     * Get tags as array
     */
    public function getTagsArrayAttribute()
    {
        if (empty($this->tags)) {
            return [];
        }
        return array_map('trim', explode(',', $this->tags));
    }
}
