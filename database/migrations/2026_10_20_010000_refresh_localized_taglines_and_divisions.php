<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * One-time: refresh localized taglines only when they still match the old seeded defaults,
     * insert division name settings if missing, and flag ur/ar/ms for native review.
     */
    public function up(): void
    {
        $taglineUpdates = [
            'site.tagline.ur' => [
                'old' => 'اپنا ٹھکانہ، بھروسے کے ساتھ',
                'new' => 'جگہوں کو بلندی دینا۔ قیام کو مالا مال بنانا۔',
                'label' => 'Tagline (ur)',
            ],
            'site.tagline.ar' => [
                'old' => 'مسكنك، بثقة',
                'new' => 'الارتقاء بالمساحات. إثراء الإقامات.',
                'label' => 'Tagline (ar)',
            ],
            'site.tagline.ms' => [
                'old' => 'Cari tempat anda, dengan kepercayaan.',
                'new' => 'Meninggikan Ruang. Memperkaya Penginapan.',
                'label' => 'Tagline (ms)',
            ],
        ];

        foreach ($taglineUpdates as $key => $pair) {
            $row = DB::table('settings')->where('key', $key)->first();
            if ($row && (string) $row->value === $pair['old']) {
                DB::table('settings')->where('key', $key)->update([
                    'value' => $pair['new'],
                    'updated_at' => now(),
                ]);
            } elseif (! $row) {
                DB::table('settings')->insert([
                    'group' => 'site',
                    'key' => $key,
                    'value' => $pair['new'],
                    'type' => 'string',
                    'label' => $pair['label'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $divisions = [
            'site.division.hotels' => ['value' => 'Maskanoor Hotels', 'label' => 'Division: Hotels'],
            'site.division.properties' => ['value' => 'Maskanoor Properties', 'label' => 'Division: Properties'],
            'site.division.living' => ['value' => 'Maskanoor Living', 'label' => 'Division: Living'],
        ];

        foreach ($divisions as $key => $meta) {
            $exists = DB::table('settings')->where('key', $key)->exists();
            if (! $exists) {
                DB::table('settings')->insert([
                    'group' => 'site',
                    'key' => $key,
                    'value' => $meta['value'],
                    'type' => 'string',
                    'label' => $meta['label'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        if (DB::getSchemaBuilder()->hasTable('languages')
            && DB::getSchemaBuilder()->hasColumn('languages', 'needs_native_review')) {
            DB::table('languages')
                ->whereIn('code', ['ur', 'ar', 'ms'])
                ->update(['needs_native_review' => true, 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        // Intentionally empty — do not revert owner-edited taglines.
    }
};
