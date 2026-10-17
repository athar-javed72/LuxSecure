<?php

namespace App\Support;

class Media
{
    /**
     * Resolve a public URL for an uploaded storage path or absolute http(s) URL.
     */
    public static function url(?string $path): string
    {
        return StorageDisk::url($path);
    }
}
