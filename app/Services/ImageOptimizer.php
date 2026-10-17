<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

class ImageOptimizer
{
    /**
     * Store an optimized JPEG on the public disk.
     */
    public function store(UploadedFile $file, string $directory = 'listings', ?int $maxWidth = null, ?int $quality = null): string
    {
        $maxWidth ??= (int) config('luxsecure.image.max_width', 1920);
        $quality ??= (int) config('luxsecure.image.quality', 82);
        $filename = Str::uuid()->toString().'.jpg';
        $path = trim($directory, '/').'/'.$filename;

        try {
            Image::configure(['driver' => extension_loaded('imagick') ? 'imagick' : 'gd']);

            $encoded = Image::make($file->getRealPath())
                ->orientate()
                ->resize($maxWidth, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('jpg', $quality);

            Storage::disk('public')->put($path, (string) $encoded);
        } catch (\Throwable) {
            $path = $file->store($directory, 'public');
        }

        return $path;
    }
}
