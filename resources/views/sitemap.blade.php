@php
    $escape = static function ($value) {
        return htmlspecialchars((string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    };
@endphp
<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($urls as $url)
    <url>
        <loc>{{ $escape($url['loc']) }}</loc>
@if (! empty($url['lastmod']))
        <lastmod>{{ $escape($url['lastmod']) }}</lastmod>
@endif
        <changefreq>{{ $escape($url['changefreq']) }}</changefreq>
        <priority>{{ $escape($url['priority']) }}</priority>
    </url>
@endforeach
</urlset>
