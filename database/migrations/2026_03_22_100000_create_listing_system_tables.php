<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['city_id', 'slug']);
            $table->index('city_id');
        });

        Schema::create('listing_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('type_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_type_id')->constrained()->cascadeOnDelete();
            $table->string('key');
            $table->string('label');
            $table->string('input_type', 20); // text, number, select, boolean
            $table->json('options')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_filterable')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['listing_type_id', 'key']);
            $table->index(['listing_type_id', 'is_filterable']);
        });

        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_type_id')->constrained('listing_types')->restrictOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('location');
            $table->string('purpose', 20); // sale, rent, short_stay
            $table->decimal('price', 15, 0);
            $table->string('price_unit', 20)->default('total'); // total, per_month, per_night
            $table->string('currency', 10)->default('PKR');
            $table->string('status', 20)->default('draft'); // draft, pending, published, rejected, sold, rented, archived
            $table->json('attributes')->nullable();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('area_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('area_size', 12, 2)->nullable();
            $table->string('area_unit', 20)->nullable(); // sq_ft, marla, kanal, sq_m
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('description')->nullable();
            $table->string('featured_image')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('listing_type_id');
            $table->index('purpose');
            $table->index('status');
            $table->index('city_id');
            $table->index('area_id');
            $table->index('price');
            $table->index(['status', 'purpose', 'listing_type_id', 'price'], 'listings_public_filter_index');
        });

        $this->migrateLegacyProperties();

        if (Schema::hasTable('property_images')) {
            Schema::table('property_images', function (Blueprint $table) {
                $table->dropForeign(['property_id']);
            });
            Schema::rename('property_images', 'listing_images');
            if (Schema::hasColumn('listing_images', 'property_id')) {
                Schema::table('listing_images', function (Blueprint $table) {
                    $table->renameColumn('property_id', 'listing_id');
                });
            }
            Schema::table('listing_images', function (Blueprint $table) {
                $table->foreign('listing_id')->references('id')->on('listings')->cascadeOnDelete();
            });
        } else {
            Schema::create('listing_images', function (Blueprint $table) {
                $table->id();
                $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
                $table->string('path');
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (Schema::hasTable('favorites') && Schema::hasColumn('favorites', 'property_id')) {
            Schema::table('favorites', function (Blueprint $table) {
                $table->dropForeign(['property_id']);
            });
            try {
                Schema::table('favorites', function (Blueprint $table) {
                    $table->dropUnique(['user_id', 'property_id']);
                });
            } catch (Throwable) {
                // Index already absent or differently named.
            }
            if (Schema::hasColumn('favorites', 'property_id')) {
                Schema::table('favorites', function (Blueprint $table) {
                    $table->renameColumn('property_id', 'listing_id');
                });
            }
            Schema::table('favorites', function (Blueprint $table) {
                $table->foreign('listing_id')->references('id')->on('listings')->cascadeOnDelete();
                $table->unique(['user_id', 'listing_id']);
            });
        }

        if (Schema::hasTable('contact_inquiries') && Schema::hasColumn('contact_inquiries', 'property_id')) {
            Schema::table('contact_inquiries', function (Blueprint $table) {
                $table->dropForeign(['property_id']);
            });
            Schema::table('contact_inquiries', function (Blueprint $table) {
                $table->renameColumn('property_id', 'listing_id');
            });
            Schema::table('contact_inquiries', function (Blueprint $table) {
                $table->foreign('listing_id')->references('id')->on('listings')->nullOnDelete();
            });
        }

        Schema::dropIfExists('properties');
    }

    public function down(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('location');
            $table->string('type', 50);
            $table->decimal('price', 15, 0);
            $table->text('description')->nullable();
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->string('featured_image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        if (Schema::hasTable('listings')) {
            $rows = DB::table('listings')->get();
            foreach ($rows as $row) {
                $type = DB::table('listing_types')->where('id', $row->listing_type_id)->value('name') ?? 'House';
                $attrs = json_decode($row->attributes ?? '{}', true) ?: [];
                DB::table('properties')->insert([
                    'id' => $row->id,
                    'title' => $row->title,
                    'location' => $row->location,
                    'type' => $type,
                    'price' => $row->price,
                    'description' => $row->description,
                    'bedrooms' => $attrs['bedrooms'] ?? null,
                    'bathrooms' => $attrs['bathrooms'] ?? null,
                    'featured_image' => $row->featured_image,
                    'is_active' => $row->status === 'published',
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                ]);
            }
        }

        if (Schema::hasTable('contact_inquiries') && Schema::hasColumn('contact_inquiries', 'listing_id')) {
            Schema::table('contact_inquiries', function (Blueprint $table) {
                $table->dropForeign(['listing_id']);
            });
            Schema::table('contact_inquiries', function (Blueprint $table) {
                $table->renameColumn('listing_id', 'property_id');
            });
            Schema::table('contact_inquiries', function (Blueprint $table) {
                $table->foreign('property_id')->references('id')->on('properties')->nullOnDelete();
            });
        }

        if (Schema::hasTable('favorites') && Schema::hasColumn('favorites', 'listing_id')) {
            Schema::table('favorites', function (Blueprint $table) {
                $table->dropForeign(['listing_id']);
                $table->dropUnique(['user_id', 'listing_id']);
            });
            Schema::table('favorites', function (Blueprint $table) {
                $table->renameColumn('listing_id', 'property_id');
            });
            Schema::table('favorites', function (Blueprint $table) {
                $table->foreign('property_id')->references('id')->on('properties')->cascadeOnDelete();
                $table->unique(['user_id', 'property_id']);
            });
        }

        if (Schema::hasTable('listing_images')) {
            Schema::table('listing_images', function (Blueprint $table) {
                $table->dropForeign(['listing_id']);
            });
            Schema::table('listing_images', function (Blueprint $table) {
                $table->renameColumn('listing_id', 'property_id');
            });
            Schema::rename('listing_images', 'property_images');
            Schema::table('property_images', function (Blueprint $table) {
                $table->foreign('property_id')->references('id')->on('properties')->cascadeOnDelete();
            });
        }

        Schema::dropIfExists('listings');
        Schema::dropIfExists('type_attributes');
        Schema::dropIfExists('listing_types');
        Schema::dropIfExists('areas');
        Schema::dropIfExists('cities');
    }

    private function migrateLegacyProperties(): void
    {
        if (! Schema::hasTable('properties')) {
            return;
        }

        $legacyTypes = DB::table('properties')->distinct()->pluck('type')->filter()->values();
        $typeIds = [];

        foreach ($legacyTypes as $typeName) {
            $slug = Str::slug((string) $typeName);
            $id = DB::table('listing_types')->insertGetId([
                'name' => $typeName,
                'slug' => $slug !== '' ? $slug : 'type-'.Str::random(6),
                'icon' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $typeIds[$typeName] = $id;

            foreach ([
                ['key' => 'bedrooms', 'label' => 'Bedrooms', 'input_type' => 'number', 'is_required' => false, 'is_filterable' => true, 'sort_order' => 1],
                ['key' => 'bathrooms', 'label' => 'Bathrooms', 'input_type' => 'number', 'is_required' => false, 'is_filterable' => true, 'sort_order' => 2],
            ] as $attr) {
                DB::table('type_attributes')->insert([
                    'listing_type_id' => $id,
                    'key' => $attr['key'],
                    'label' => $attr['label'],
                    'input_type' => $attr['input_type'],
                    'options' => null,
                    'is_required' => $attr['is_required'],
                    'is_filterable' => $attr['is_filterable'],
                    'sort_order' => $attr['sort_order'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $fallbackTypeId = $typeIds[array_key_first($typeIds)] ?? DB::table('listing_types')->insertGetId([
            'name' => 'House',
            'slug' => 'house',
            'icon' => null,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $usedSlugs = [];
        foreach (DB::table('properties')->orderBy('id')->get() as $property) {
            $cityName = trim((string) Str::afterLast($property->location, ','));
            if ($cityName === '' || $cityName === $property->location) {
                $cityName = trim(explode(',', (string) $property->location)[0] ?? 'Unknown');
            }
            $citySlug = Str::slug($cityName) ?: 'city-'.$property->id;
            $cityId = DB::table('cities')->where('slug', $citySlug)->value('id');
            if (! $cityId) {
                $cityId = DB::table('cities')->insertGetId([
                    'name' => $cityName,
                    'slug' => $citySlug,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $baseSlug = Str::slug($property->title) ?: 'listing-'.$property->id;
            $slug = $baseSlug;
            $i = 1;
            while (isset($usedSlugs[$slug]) || DB::table('listings')->where('slug', $slug)->exists()) {
                $slug = $baseSlug.'-'.$i++;
            }
            $usedSlugs[$slug] = true;

            $typeId = $typeIds[$property->type] ?? $fallbackTypeId;
            $attrs = array_filter([
                'bedrooms' => $property->bedrooms,
                'bathrooms' => $property->bathrooms,
            ], fn ($v) => $v !== null);

            DB::table('listings')->insert([
                'id' => $property->id,
                'listing_type_id' => $typeId,
                'title' => $property->title,
                'slug' => $slug,
                'location' => $property->location,
                'purpose' => 'sale',
                'price' => $property->price,
                'price_unit' => 'total',
                'currency' => 'PKR',
                'status' => $property->is_active ? 'published' : 'draft',
                'attributes' => json_encode((object) $attrs),
                'city_id' => $cityId,
                'area_id' => null,
                'area_size' => null,
                'area_unit' => null,
                'owner_id' => null,
                'agent_id' => null,
                'description' => $property->description,
                'featured_image' => $property->featured_image,
                'created_at' => $property->created_at ?? now(),
                'updated_at' => $property->updated_at ?? now(),
                'deleted_at' => null,
            ]);
        }

        // Keep auto-increment in sync on MySQL.
        if (DB::getDriverName() === 'mysql' && DB::table('listings')->count() > 0) {
            $maxId = (int) DB::table('listings')->max('id');
            DB::statement('ALTER TABLE listings AUTO_INCREMENT = '.($maxId + 1));
        }
    }
};
