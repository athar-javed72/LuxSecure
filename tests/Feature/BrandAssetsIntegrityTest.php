<?php

namespace Tests\Feature;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Tests\TestCase;

class BrandAssetsIntegrityTest extends TestCase
{
    public function test_public_brand_svgs_match_brand_source_byte_for_byte(): void
    {
        $sourceRoot = base_path('brand-source/maskanoor-brand');
        $publicRoot = public_path('brand');

        $this->assertDirectoryExists($sourceRoot);
        $this->assertDirectoryExists($publicRoot);

        $folders = ['logos', 'monogram', 'divisions', 'favicon'];
        $checked = 0;

        foreach ($folders as $folder) {
            $sourceDir = $sourceRoot.DIRECTORY_SEPARATOR.$folder;
            $publicDir = $publicRoot.DIRECTORY_SEPARATOR.$folder;
            $this->assertDirectoryExists($sourceDir);
            $this->assertDirectoryExists($publicDir);

            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($sourceDir, RecursiveDirectoryIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if (! $file->isFile() || strtolower($file->getExtension()) !== 'svg') {
                    continue;
                }

                $relative = $folder.DIRECTORY_SEPARATOR.$file->getFilename();
                $publicPath = $publicDir.DIRECTORY_SEPARATOR.$file->getFilename();

                $this->assertFileExists($publicPath, "Missing public brand SVG: {$relative}");

                $sourceHash = hash_file('sha256', $file->getPathname());
                $publicHash = hash_file('sha256', $publicPath);

                $this->assertSame(
                    $sourceHash,
                    $publicHash,
                    "SHA-256 mismatch for {$relative}"
                );
                $checked++;
            }
        }

        $this->assertGreaterThan(0, $checked, 'Expected to verify at least one SVG');
    }
}
