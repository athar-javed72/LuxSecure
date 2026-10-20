<?php

namespace App\Services;

use App\Models\MediaAsset;
use App\Support\StorageDisk;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Intervention\Image\ImageManagerStatic as Image;

class MediaUploadService
{
    public function storeImage(UploadedFile $file, string $directory = 'media'): array
    {
        $this->assertAllowedImage($file);

        $base = Str::uuid()->toString();
        $disk = StorageDisk::uploads();
        $diskFs = StorageDisk::uploadsDisk();
        $widths = config('media_slots.limits.variant_widths', [480, 800, 1200, 1600, 2400]);

        Image::configure(['driver' => extension_loaded('imagick') ? 'imagick' : 'gd']);
        $image = Image::make($file->getRealPath())->orientate();
        // Strip EXIF/GPS by re-encoding without profiles.
        $image->getCore();

        $variants = [];
        $primaryPath = null;
        $primaryWidth = 0;
        $primaryHeight = 0;

        foreach ($widths as $width) {
            $clone = clone $image;
            $clone->resize($width, null, function ($c) {
                $c->aspectRatio();
                $c->upsize();
            });

            foreach (['webp' => 82, 'jpg' => 82] as $ext => $quality) {
                // AVIF when Imagick supports it; otherwise skip silently.
                if ($ext === 'webp' && ! function_exists('imagewebp') && ! extension_loaded('imagick')) {
                    continue;
                }
                $path = trim($directory, '/').'/'.$base.'-'.$width.'.'.$ext;
                try {
                    $encoded = $clone->encode($ext === 'jpg' ? 'jpg' : $ext, $quality);
                    $diskFs->put($path, (string) $encoded);
                    $variants[$ext][$width] = $path;
                    if ($ext === 'jpg' && ($primaryPath === null || $width > $primaryWidth)) {
                        $primaryPath = $path;
                        $primaryWidth = $clone->width();
                        $primaryHeight = $clone->height();
                    }
                } catch (\Throwable) {
                    // skip unsupported encode
                }
            }

            if (extension_loaded('imagick')) {
                try {
                    $path = trim($directory, '/').'/'.$base.'-'.$width.'.avif';
                    $encoded = (clone $clone)->encode('avif', 60);
                    $diskFs->put($path, (string) $encoded);
                    $variants['avif'][$width] = $path;
                } catch (\Throwable) {
                    // optional
                }
            }
        }

        if (! $primaryPath) {
            $primaryPath = $file->storeAs(trim($directory, '/'), $base.'.jpg', $disk);
            $primaryWidth = $image->width();
            $primaryHeight = $image->height();
        }

        return [
            'disk' => $disk,
            'path' => $primaryPath,
            'meta' => [
                'width' => $primaryWidth,
                'height' => $primaryHeight,
                'variants' => $variants,
            ],
        ];
    }

    public function storeVideo(UploadedFile $file, string $directory = 'media/videos'): array
    {
        $this->assertAllowedVideo($file);
        $ext = strtolower($file->getClientOriginalExtension() ?: 'mp4');
        if (! in_array($ext, ['mp4', 'webm'], true)) {
            $ext = 'mp4';
        }
        $filename = Str::uuid()->toString().'.'.$ext;
        $path = $file->storeAs(trim($directory, '/'), $filename, StorageDisk::uploads());

        return [
            'disk' => StorageDisk::uploads(),
            'path' => $path,
            'meta' => [
                'bytes' => $file->getSize(),
                'mime' => $this->finfoMime($file),
            ],
        ];
    }

    public function parseEmbedUrl(string $url): array
    {
        $url = trim($url);
        if (preg_match('~(?:youtube\.com/watch\?v=|youtu\.be/|youtube\.com/embed/|youtube-nocookie\.com/embed/)([A-Za-z0-9_-]{6,})~', $url, $m)) {
            return ['provider' => 'youtube', 'id' => $m[1]];
        }
        if (preg_match('~vimeo\.com/(?:video/)?(\d+)~', $url, $m)) {
            return ['provider' => 'vimeo', 'id' => $m[1]];
        }
        if (preg_match('~^[A-Za-z0-9_-]{6,}$~', $url)) {
            return ['provider' => 'youtube', 'id' => $url];
        }
        if (preg_match('~^\d{6,}$~', $url)) {
            return ['provider' => 'vimeo', 'id' => $url];
        }

        throw ValidationException::withMessages(['embed_url' => 'Enter a valid YouTube or Vimeo URL or video ID.']);
    }

    public function deleteFiles(?MediaAsset $asset): void
    {
        if (! $asset) {
            return;
        }
        $disk = StorageDisk::uploadsDisk();
        foreach (array_filter([$asset->path, $asset->poster_path]) as $path) {
            if ($path && ! str_starts_with($path, '/') && $disk->exists($path)) {
                $disk->delete($path);
            }
        }
        foreach ($asset->variantUrls() as $byExt) {
            if (! is_array($byExt)) {
                continue;
            }
            foreach ($byExt as $path) {
                if (is_string($path) && $disk->exists($path)) {
                    $disk->delete($path);
                }
            }
        }
    }

    private function assertAllowedImage(UploadedFile $file): void
    {
        $maxKb = (int) config('media_slots.limits.image_max_kb', 8192);
        if ($file->getSize() > $maxKb * 1024) {
            throw ValidationException::withMessages(['file' => 'Image must be '.$maxKb.'KB or smaller.']);
        }
        $mime = $this->finfoMime($file);
        $clientMime = strtolower((string) $file->getMimeType());
        $allowed = config('media_slots.limits.image_mimes', []);
        if (! in_array($mime, $allowed, true) && ! in_array($clientMime, $allowed, true)) {
            throw ValidationException::withMessages(['file' => 'Unsupported image type. Use JPG, PNG, WebP or AVIF.']);
        }
        $ext = strtolower($file->getClientOriginalExtension() ?: '');
        if (in_array($ext, ['svg', 'svgz', 'exe', 'php', 'js', 'html'], true)) {
            throw ValidationException::withMessages(['file' => 'This file type is not allowed.']);
        }
    }

    private function assertAllowedVideo(UploadedFile $file): void
    {
        $maxKb = (int) config('media_slots.limits.video_max_kb', 51200);
        if ($file->getSize() > $maxKb * 1024) {
            throw ValidationException::withMessages(['file' => 'Video must be '.$maxKb.'KB or smaller.']);
        }
        $mime = $this->finfoMime($file);
        $allowed = config('media_slots.limits.video_mimes', []);
        if (! in_array($mime, $allowed, true)) {
            throw ValidationException::withMessages(['file' => 'Unsupported video type. Use MP4 or WebM.']);
        }
    }

    private function finfoMime(UploadedFile $file): string
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file->getRealPath()) ?: '';

        return strtolower((string) $mime);
    }
}
