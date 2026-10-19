<?php

namespace App\Services;

use App\Support\StorageDisk;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

class ImageOptimizer
{
    /**
     * Store an optimized JPEG on the configured uploads disk.
     */
    public function store(UploadedFile $file, string $directory = 'listings', ?int $maxWidth = null, ?int $quality = null): string
    {
        $maxWidth ??= (int) config('maskanoor.image.max_width', 1920);
        $quality ??= (int) config('maskanoor.image.quality', 82);
        $filename = Str::uuid()->toString().'.jpg';
        $path = trim($directory, '/').'/'.$filename;
        $disk = StorageDisk::uploads();

        try {
            Image::configure(['driver' => extension_loaded('imagick') ? 'imagick' : 'gd']);

            $encoded = Image::make($file->getRealPath())
                ->orientate()
                ->resize($maxWidth, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('jpg', $quality);

            StorageDisk::uploadsDisk()->put($path, (string) $encoded);
        } catch (\Throwable) {
            $path = $file->store($directory, $disk);
        }

        return $path;
    }
}
