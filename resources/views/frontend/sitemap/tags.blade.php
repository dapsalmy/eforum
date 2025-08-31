<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:xhtml="https://www.w3.org/1999/xhtml" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
    xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd
    https://www.w3.org/1999/xhtml https://www.w3.org/2002/08/xhtml/xhtml1-strict.xsd">

    <url>
        <loc>{{ url('') }}/tags</loc>
        <lastmod>2024-09-20T14:59:41+00:00</lastmod>
        <changefreq>always</changefreq>
        <priority>0.85</priority>
    </url>
    @foreach ($items as $tag)
        <url>
            <loc>{{ url('') }}/tag/{{ $tag->slug }}</loc>
            <changefreq>weekly</changefreq>
            <priority>0.80</priority>
        </url>
    @endforeach
</urlset>
