<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">
    
    <!-- Homepage -->
    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ date('Y-m-d') }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    
    <!-- Places Listing Page -->
    <url>
        <loc>{{ url('/places') }}</loc>
        <lastmod>{{ date('Y-m-d') }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>

    <!-- Location Recommendations Page -->
    <url>
        <loc>{{ url('/recommendations') }}</loc>
        <lastmod>{{ date('Y-m-d') }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    
    <!-- Individual Places with Images -->
    @foreach($places as $place)
    <url>
        <loc>{{ url('/places/' . $place->id) }}</loc>
        <lastmod>{{ $place->updated_at ? $place->updated_at->format('Y-m-d') : date('Y-m-d') }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
        @if($place->url_gambar)
        <image:image>
            <image:loc>{{ $place->url_gambar }}</image:loc>
            <image:title>{{ htmlspecialchars($place->nama, ENT_XML1, 'UTF-8') }}</image:title>
            <image:caption>Wisata {{ htmlspecialchars($place->nama, ENT_XML1, 'UTF-8') }} di Bogor</image:caption>
        </image:image>
        @endif
    </url>
    @endforeach
    
</urlset>
