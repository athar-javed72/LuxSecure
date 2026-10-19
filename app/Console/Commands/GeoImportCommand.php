<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Country;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GeoImportCommand extends Command
{
    protected $signature = 'maskanoor:geo:import
        {--source= : Path to geo dataset directory (default storage/app/geo)}
        {--country= : Limit to ISO2 code(s), comma-separated}
        {--chunk=1000 : Chunk size for city upserts}
        {--dry-run : Parse and report without writing}';

    protected $description = 'Import countries/states/cities from the dr5hn countries-states-cities dataset (idempotent by source_id).';

    public function handle(): int
    {
        // Full cities.json is ~190 MB; decoding needs headroom beyond PHP's default 128M.
        if ((int) ini_get('memory_limit') > 0 && $this->memoryLimitBytes() < 1024 * 1024 * 1024) {
            ini_set('memory_limit', '1024M');
        }

        $source = $this->option('source') ?: storage_path('app/geo');
        $dry = (bool) $this->option('dry-run');
        $chunk = max(100, (int) $this->option('chunk'));
        $limitIso = collect(explode(',', (string) $this->option('country')))
            ->map(fn ($v) => strtoupper(trim($v)))
            ->filter()
            ->all();

        $countriesFile = $source.DIRECTORY_SEPARATOR.'countries.json';
        $statesFile = $source.DIRECTORY_SEPARATOR.'states.json';
        $citiesFile = $source.DIRECTORY_SEPARATOR.'cities.json';

        foreach ([$countriesFile, $statesFile, $citiesFile] as $file) {
            if (! File::exists($file)) {
                $this->error("Missing {$file}");
                $this->line('See docs/GEO_DATA.md for download instructions.');

                return self::FAILURE;
            }
        }

        $this->info('Loading dataset…');
        $countries = json_decode(File::get($countriesFile), true) ?: [];
        $states = json_decode(File::get($statesFile), true) ?: [];
        $cities = json_decode(File::get($citiesFile), true) ?: [];

        if ($limitIso !== []) {
            $countries = array_values(array_filter(
                $countries,
                fn ($c) => in_array(strtoupper((string) ($c['iso2'] ?? '')), $limitIso, true)
            ));
            $allowedIds = collect($countries)->pluck('id')->all();
            $states = array_values(array_filter($states, fn ($s) => in_array($s['country_id'] ?? null, $allowedIds, true)));
            $cities = array_values(array_filter($cities, fn ($c) => in_array($c['country_id'] ?? null, $allowedIds, true)));
        }

        $this->line(sprintf('Countries: %d · States: %d · Cities: %d%s', count($countries), count($states), count($cities), $dry ? ' (dry-run)' : ''));

        $statesByCountry = collect($states)->groupBy(fn ($s) => (int) ($s['country_id'] ?? 0));
        $citiesByCountry = collect($cities)->groupBy(fn ($c) => (int) ($c['country_id'] ?? 0));

        $bar = $this->output->createProgressBar(count($countries));
        $bar->start();

        $createdCountries = 0;
        $matchedCities = 0;
        $upsertedCities = 0;

        foreach ($countries as $row) {
            $iso2 = strtoupper((string) ($row['iso2'] ?? ''));
            $sourceId = (int) ($row['id'] ?? 0);
            if ($iso2 === '' || $sourceId < 1) {
                $bar->advance();

                continue;
            }

            if ($dry) {
                $bar->advance();

                continue;
            }

            DB::transaction(function () use ($row, $iso2, $sourceId, $statesByCountry, $citiesByCountry, $chunk, &$createdCountries, &$matchedCities, &$upsertedCities) {
                $existing = Country::query()->where('iso2', $iso2)->orWhere('source_id', $sourceId)->first();
                $wasActive = $existing?->is_active;

                if (! $existing) {
                    $existing = Country::query()->create([
                        'iso2' => $iso2,
                        'iso3' => $row['iso3'] ?? null,
                        'source_id' => $sourceId,
                        'name' => $row['name'] ?? $iso2,
                        'currency_code' => $row['currency'] ?? null,
                        'default_locale' => 'en',
                        'timezone' => is_array($row['timezones'] ?? null) ? ($row['timezones'][0]['zoneName'] ?? 'UTC') : 'UTC',
                        'phone_code' => isset($row['phonecode']) ? (string) $row['phonecode'] : null,
                        'is_active' => false,
                        'sort_order' => 999,
                    ]);
                    $createdCountries++;
                } else {
                    $patch = [
                        'iso3' => $row['iso3'] ?? $existing->iso3,
                        'source_id' => $sourceId,
                    ];
                    // Do not overwrite operational settings on already-activated countries.
                    if (! $wasActive) {
                        $patch['name'] = $row['name'] ?? $existing->name;
                        if (! filled($existing->currency_code) && ! empty($row['currency'])) {
                            $patch['currency_code'] = $row['currency'];
                        }
                    }
                    $existing->fill($patch)->save();
                }

                $stateIndex = collect($statesByCountry->get($sourceId, []))
                    ->keyBy(fn ($s) => (int) ($s['id'] ?? 0));

                $countryCities = $citiesByCountry->get($sourceId, collect())->values()->all();
                foreach (array_chunk($countryCities, $chunk) as $batch) {
                    foreach ($batch as $cityRow) {
                        $citySourceId = (int) ($cityRow['id'] ?? 0);
                        $name = trim((string) ($cityRow['name'] ?? ''));
                        if ($citySourceId < 1 || $name === '') {
                            continue;
                        }

                        $stateId = (int) ($cityRow['state_id'] ?? 0);
                        $state = $stateIndex->get($stateId);
                        $stateName = $state['name'] ?? ($cityRow['state_name'] ?? null);
                        $stateCode = $state['state_code'] ?? ($cityRow['state_code'] ?? null);

                        $matched = City::query()
                            ->where('source_id', $citySourceId)
                            ->first();

                        if (! $matched) {
                            $matched = City::query()
                                ->where('country_id', $existing->id)
                                ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
                                ->when($stateCode, fn ($q) => $q->where(function ($qq) use ($stateCode) {
                                    $qq->whereNull('state_code')->orWhere('state_code', $stateCode);
                                }))
                                ->orderBy('id')
                                ->first();
                            if ($matched) {
                                $matchedCities++;
                            }
                        }

                        $slugBase = Str::slug(($stateCode ? $stateCode.'-' : '').$name) ?: 'city-'.$citySourceId;
                        $slug = $slugBase;

                        $payload = [
                            'country_id' => $existing->id,
                            'name' => $name,
                            'slug' => $matched?->slug ?: $slug,
                            'state_name' => $stateName,
                            'state_code' => $stateCode,
                            'source_id' => $citySourceId,
                            'latitude' => $cityRow['latitude'] ?? null,
                            'longitude' => $cityRow['longitude'] ?? null,
                            'population' => isset($cityRow['population']) ? (int) $cityRow['population'] : ($matched?->population),
                            'is_active' => $matched?->is_active ?? true,
                        ];

                        if ($matched) {
                            // Preserve id and existing slug; attach source_id / geo fields.
                            $matched->fill(collect($payload)->except(['slug'])->all())->save();
                        } else {
                            // Ensure country-scoped slug uniqueness.
                            $i = 1;
                            while (City::query()->where('country_id', $existing->id)->where('slug', $slug)->exists()) {
                                $slug = $slugBase.'-'.$i++;
                            }
                            $payload['slug'] = $slug;
                            City::query()->create($payload);
                            $upsertedCities++;
                        }
                    }
                }
            });

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        if ($dry) {
            $this->warn('Dry-run complete — no rows written.');
        } else {
            $this->info("Countries created: {$createdCountries}");
            $this->info("Existing cities matched (ids preserved): {$matchedCities}");
            $this->info("Cities created: {$upsertedCities}");
        }

        return self::SUCCESS;
    }

    private function memoryLimitBytes(): int
    {
        $raw = (string) ini_get('memory_limit');
        if ($raw === '-1') {
            return PHP_INT_MAX;
        }

        $unit = strtolower(substr($raw, -1));
        $value = (int) $raw;

        return match ($unit) {
            'g' => $value * 1024 * 1024 * 1024,
            'm' => $value * 1024 * 1024,
            'k' => $value * 1024,
            default => (int) $raw,
        };
    }
}
