<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminAndPropertiesSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@luxsecure.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
        if (!$admin->isAdmin()) {
            $admin->update(['role' => 'admin', 'email_verified_at' => $admin->email_verified_at ?? now()]);
        }

        $samples = [
            ['title' => 'Modern Family House', 'location' => 'DHA Phase 6, Karachi', 'type' => 'House', 'price' => 52000000, 'bedrooms' => 5, 'bathrooms' => 4, 'featured_image' => 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1200&q=80'],
            ['title' => 'Luxury Villa', 'location' => 'Bahria Town, Lahore', 'type' => 'Villa', 'price' => 85000000, 'bedrooms' => 6, 'bathrooms' => 5, 'featured_image' => 'https://images.unsplash.com/photo-1613490493576-7fde63acd811?auto=format&fit=crop&w=1200&q=80'],
            ['title' => 'Cozy Apartment', 'location' => 'Gulberg, Islamabad', 'type' => 'Apartment', 'price' => 21000000, 'bedrooms' => 2, 'bathrooms' => 2, 'featured_image' => 'https://images.unsplash.com/photo-1502673530728-f79b4cab31b1?auto=format&fit=crop&w=1200&q=80'],
            ['title' => 'Classic Bungalow', 'location' => 'Clifton, Karachi', 'type' => 'Bungalow', 'price' => 63000000, 'bedrooms' => 4, 'bathrooms' => 4, 'featured_image' => 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=1200&q=80'],
            ['title' => 'Canal View Residence', 'location' => 'Canal Road, Lahore', 'type' => 'House', 'price' => 48000000, 'bedrooms' => 4, 'bathrooms' => 3, 'featured_image' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80'],
            ['title' => 'Skyline Penthouse', 'location' => 'Blue Area, Islamabad', 'type' => 'Apartment', 'price' => 129000000, 'bedrooms' => 3, 'bathrooms' => 3, 'featured_image' => 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=1200&q=80'],
            ['title' => 'Golf Facing Villa', 'location' => 'DHA Valley, Islamabad', 'type' => 'Villa', 'price' => 97000000, 'bedrooms' => 5, 'bathrooms' => 5, 'featured_image' => 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1200&q=80'],
            ['title' => 'Elegant Residence', 'location' => 'E-11, Islamabad', 'type' => 'House', 'price' => 45000000, 'bedrooms' => 4, 'bathrooms' => 3, 'featured_image' => 'https://images.unsplash.com/photo-1600047509807-ba8f99d2cd00?auto=format&fit=crop&w=1200&q=80'],
            ['title' => 'Defence Phase 5 House', 'location' => 'Defence Phase 5, Lahore', 'type' => 'House', 'price' => 72000000, 'bedrooms' => 5, 'bathrooms' => 4, 'featured_image' => 'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=1200&q=80'],
            ['title' => 'Hayatabad Family Home', 'location' => 'Hayatabad, Peshawar', 'type' => 'House', 'price' => 38000000, 'bedrooms' => 4, 'bathrooms' => 3, 'featured_image' => 'https://images.unsplash.com/photo-1600573472592-401b489a3cdc?auto=format&fit=crop&w=1200&q=80'],
            ['title' => 'Bahria Heights Apartment', 'location' => 'Bahria Town, Rawalpindi', 'type' => 'Apartment', 'price' => 26000000, 'bedrooms' => 2, 'bathrooms' => 2, 'featured_image' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80'],
            ['title' => 'Bosan Road Villa', 'location' => 'Bosan Road, Multan', 'type' => 'Villa', 'price' => 54000000, 'bedrooms' => 5, 'bathrooms' => 4, 'featured_image' => 'https://images.unsplash.com/photo-1600585154526-990dced4db0d?auto=format&fit=crop&w=1200&q=80'],
            ['title' => 'Canal View Home', 'location' => 'D Ground, Faisalabad', 'type' => 'House', 'price' => 32000000, 'bedrooms' => 3, 'bathrooms' => 3, 'featured_image' => 'https://images.unsplash.com/photo-1605276374104-dee2a0ed3cd6?auto=format&fit=crop&w=1200&q=80'],
            ['title' => 'Murree Hills Cottage', 'location' => 'Murree', 'type' => 'House', 'price' => 68000000, 'bedrooms' => 3, 'bathrooms' => 2, 'featured_image' => 'https://images.unsplash.com/photo-1518780664697-55e3ad937233?auto=format&fit=crop&w=1200&q=80'],
            ['title' => 'Karimabad View Residence', 'location' => 'Hunza Valley, Karimabad', 'type' => 'Villa', 'price' => 115000000, 'bedrooms' => 4, 'bathrooms' => 4, 'featured_image' => 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1200&q=80&sig=15'],
            ['title' => 'Gwadar Sea View Apartment', 'location' => 'Gwadar', 'type' => 'Apartment', 'price' => 49000000, 'bedrooms' => 3, 'bathrooms' => 2, 'featured_image' => 'https://images.unsplash.com/photo-1493809842364-78817add7ffb?auto=format&fit=crop&w=1200&q=80'],
            ['title' => 'Sialkot Cantt Bungalow', 'location' => 'Sialkot Cantt', 'type' => 'Bungalow', 'price' => 56000000, 'bedrooms' => 4, 'bathrooms' => 3, 'featured_image' => 'https://images.unsplash.com/photo-1570129477492-45c003edd2be?auto=format&fit=crop&w=1200&q=80'],
        ];

        // Ensure every sample uses a distinct Unsplash photo id.
        $uniqueImages = [
            'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1613490493576-7fde63acd811?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1502673530728-f79b4cab31b1?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1600047509807-ba8f99d2cd00?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1600573472592-401b489a3cdc?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1600585154526-990dced4db0d?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1605276374104-dee2a0ed3cd6?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1518780664697-55e3ad937233?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1449844908441-8829872d2607?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1493809842364-78817add7ffb?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1570129477492-45c003edd2be?auto=format&fit=crop&w=1200&q=80',
        ];

        foreach ($samples as $i => &$sample) {
            $sample['featured_image'] = $uniqueImages[$i];
            $sample['is_active'] = true;
            $sample['description'] = $sample['description'] ?? 'A carefully verified FindState listing with modern finishes and a prime location.';
        }
        unset($sample);

        if (Property::count() === 0) {
            foreach ($samples as $s) {
                Property::create($s);
            }
            return;
        }

        // Backfill unique images / beds / baths for existing seeded rows.
        $properties = Property::orderBy('id')->get();
        foreach ($properties as $index => $property) {
            $sample = $samples[$index % count($samples)];
            $property->update([
                'featured_image' => $uniqueImages[$index % count($uniqueImages)],
                'bedrooms' => $property->bedrooms ?: $sample['bedrooms'],
                'bathrooms' => $property->bathrooms ?: $sample['bathrooms'],
                'description' => $property->description ?: $sample['description'],
            ]);
        }
    }
}
