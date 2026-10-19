<?php

namespace App\Support;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

class StorageDisk
{
    public static function uploads(): string
    {
        return config('luxsecure.uploads_disk', env('UPLOADS_DISK', 'public'));
    }

    public static function private(): string
    {
        return config('luxsecure.private_disk', env('PRIVATE_DISK', 'local'));
    }

    public static function uploadsDisk(): Filesystem
    {
        return Storage::disk(static::uploads());
    }

    public static function privateDisk(): Filesystem
    {
        return Storage::disk(static::private());
    }

    public static function url(?string $path): string
    {
        if ($path === null || trim($path) === '') {
            return '';
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        // Public web paths (e.g. /images/listings/...) — not storage disk keys
        if (str_starts_with($path, '/')) {
            return $path;
        }

        $disk = static::uploads();

        try {
            return Storage::disk($disk)->url($path);
        } catch (\Throwable) {
            return asset('storage/'.ltrim($path, '/'));
        }
    }
}
