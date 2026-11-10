<?php

if (!function_exists('hd_asset')) {
    /**
     * Folder asset URL with a cache-busting version query string.
     *
     * Logos and slider images are always served from public/assets, never from
     * cached copies or the database. Bump config('hosteldock.asset_version') to
     * force browsers to re-fetch a replaced folder file.
     */
    function hd_asset(string $path): string
    {
        $url = asset($path);
        $version = (string) config('hosteldock.asset_version', '1');

        if ($version === '') {
            return $url;
        }

        return $url . (strpos($url, '?') === false ? '?' : '&') . 'v=' . rawurlencode($version);
    }
}