<?php

namespace Database\Seeders;

use App\Models\MediaAsset;
use Illuminate\Database\Seeder;

/**
 * Seeds is_demo media pointing at approved public/images assets.
 * Hidden automatically when APP_ENV=production.
 */
class DemoMediaSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            return;
        }

        $map = [
            'home.hero' => [
                ['path' => '/images/home/hero-1.jpg', 'alt' => 'Warm hotel suite with soft evening light'],
                ['path' => '/images/home/hero-2.jpg', 'alt' => 'Residence exterior at dusk'],
                ['path' => '/images/home/hero-3.jpg', 'alt' => 'Living room with quiet window light'],
            ],
            'home.intro' => [['path' => '/images/home/about.jpg', 'alt' => 'Architectural detail']],
            'home.division.hotels' => [['path' => '/images/divisions/hotels.jpg', 'alt' => 'Hotels division']],
            'home.division.properties' => [['path' => '/images/divisions/properties.jpg', 'alt' => 'Properties division']],
            'home.division.living' => [['path' => '/images/divisions/living.jpg', 'alt' => 'Living division']],
            'home.about' => [['path' => '/images/home/about.jpg', 'alt' => 'About']],
            'home.break' => [['path' => '/images/home/break.jpg', 'alt' => 'Editorial break']],
            'hotels.hero' => [
                ['path' => '/images/divisions/hotels.jpg', 'alt' => 'Hotels hero'],
                ['path' => '/images/listings/hotel-1.jpg', 'alt' => 'Hotel interior'],
            ],
            'hotels.intro' => [['path' => '/images/listings/hotel-1.jpg', 'alt' => 'Hotels intro']],
            'hotels.gallery' => [
                ['path' => '/images/listings/hotel-1.jpg', 'alt' => 'Stay gallery 1'],
                ['path' => '/images/home/hero-1.jpg', 'alt' => 'Stay gallery 2'],
                ['path' => '/images/divisions/hotels.jpg', 'alt' => 'Stay gallery 3'],
            ],
            'living.hero' => [
                ['path' => '/images/divisions/living.jpg', 'alt' => 'Living hero'],
                ['path' => '/images/listings/apartment-1.jpg', 'alt' => 'Apartment living'],
            ],
            'living.intro' => [['path' => '/images/listings/apartment-2.jpg', 'alt' => 'Living intro']],
            'living.gallery' => [
                ['path' => '/images/listings/apartment-1.jpg', 'alt' => 'Rental gallery 1'],
                ['path' => '/images/listings/apartment-2.jpg', 'alt' => 'Rental gallery 2'],
                ['path' => '/images/listings/house-1.jpg', 'alt' => 'Rental gallery 3'],
            ],
        ];

        foreach ($map as $slot => $items) {
            foreach ($items as $i => $item) {
                MediaAsset::query()->updateOrCreate(
                    ['slot' => $slot, 'path' => $item['path'], 'is_demo' => true],
                    [
                        'type' => 'image',
                        'disk' => 'public',
                        'alt' => $item['alt'],
                        'decorative' => false,
                        'focal_x' => 50,
                        'focal_y' => 50,
                        'sort' => $i,
                        'is_active' => true,
                        'meta' => ['width' => 2400, 'height' => 1350],
                    ]
                );
            }
        }
    }
}
