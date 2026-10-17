<?php

namespace App\Support;

class Media
{
    /**
     * Resolve a public URL for an uploaded storage path or absolute http(s) URL.
     */
    public static function url(?string $path): string
    {
        if ($path === null || trim($path) === '') {
            return '';
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset('storage/'.ltrim($path, '/'));
    }
}
