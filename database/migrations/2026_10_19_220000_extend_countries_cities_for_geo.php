<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            if (! Schema::hasColumn('countries', 'iso3')) {
                $table->char('iso3', 3)->nullable()->after('iso2');
            }
            if (! Schema::hasColumn('countries', 'source_id')) {
                $table->unsignedBigInteger('source_id')->nullable()->after('iso3');
            }
            if (! Schema::hasColumn('countries', 'wind_down')) {
                $table->boolean('wind_down')->default(false)->after('is_active');
            }
        });

        // Operational fields nullable so inactive master countries can exist without full setup.
        // (Fresh installs already get nullable columns from the countries create migration.)
        $this->nullableString('countries', 'currency_code', 3);
        $this->nullableString('countries', 'default_locale', 12);
        $this->nullableString('countries', 'timezone', 64);

        if (Schema::hasColumn('countries', 'tax_percent')) {
            // Keep column; allow null via raw for SQLite/MySQL portability where needed.
            try {
                DB::statement('ALTER TABLE countries MODIFY tax_percent DECIMAL(8,4) NULL');
            } catch (Throwable) {
                // SQLite ignores MODIFY; fine — defaults still work.
            }
        }

        Schema::table('countries', function (Blueprint $table) {
            if (! $this->indexExists('countries', 'countries_iso3_unique') && Schema::hasColumn('countries', 'iso3')) {
                $table->unique('iso3');
            }
            if (! $this->indexExists('countries', 'countries_source_id_unique') && Schema::hasColumn('countries', 'source_id')) {
                $table->unique('source_id');
            }
        });

        Schema::table('cities', function (Blueprint $table) {
            if (! Schema::hasColumn('cities', 'state_name')) {
                $table->string('state_name')->nullable()->after('name');
            }
            if (! Schema::hasColumn('cities', 'state_code')) {
                $table->string('state_code', 16)->nullable()->after('state_name');
            }
            if (! Schema::hasColumn('cities', 'source_id')) {
                $table->unsignedBigInteger('source_id')->nullable()->after('state_code');
            }
            if (! Schema::hasColumn('cities', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('source_id');
            }
            if (! Schema::hasColumn('cities', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
            if (! Schema::hasColumn('cities', 'population')) {
                $table->unsignedBigInteger('population')->nullable()->after('longitude');
            }
        });

        // Replace global slug unique with (country_id, slug) where possible.
        $this->dropIndexIfExists('cities', 'cities_slug_unique');

        Schema::table('cities', function (Blueprint $table) {
            if (! $this->indexExists('cities', 'cities_source_id_unique') && Schema::hasColumn('cities', 'source_id')) {
                $table->unique('source_id');
            }
            if (! $this->indexExists('cities', 'cities_country_state_name_unique')) {
                $table->unique(['country_id', 'state_code', 'name'], 'cities_country_state_name_unique');
            }
            if (! $this->indexExists('cities', 'cities_country_id_name_index')) {
                $table->index(['country_id', 'name'], 'cities_country_id_name_index');
            }
            if (! $this->indexExists('cities', 'cities_country_id_slug_unique') && Schema::hasColumn('cities', 'slug')) {
                $table->unique(['country_id', 'slug'], 'cities_country_id_slug_unique');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            foreach ([
                'cities_country_state_name_unique',
                'cities_country_id_name_index',
                'cities_country_id_slug_unique',
                'cities_source_id_unique',
            ] as $index) {
                try {
                    $table->dropUnique($index);
                } catch (Throwable) {
                    try {
                        $table->dropIndex($index);
                    } catch (Throwable) {
                    }
                }
            }
            foreach (['state_name', 'state_code', 'source_id', 'latitude', 'longitude', 'population'] as $col) {
                if (Schema::hasColumn('cities', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('countries', function (Blueprint $table) {
            foreach (['countries_iso3_unique', 'countries_source_id_unique'] as $index) {
                try {
                    $table->dropUnique($index);
                } catch (Throwable) {
                }
            }
            foreach (['iso3', 'source_id', 'wind_down'] as $col) {
                if (Schema::hasColumn('countries', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    private function nullableString(string $table, string $column, int $length): void
    {
        if (! Schema::hasColumn($table, $column)) {
            return;
        }
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            try {
                DB::statement("ALTER TABLE {$table} MODIFY {$column} VARCHAR({$length}) NULL");
            } catch (Throwable) {
            }
        }
        // SQLite cannot easily change nullability; leave as-is.
    }

    private function indexExists(string $table, string $index): bool
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'sqlite') {
            $rows = DB::select("PRAGMA index_list('{$table}')");
            foreach ($rows as $row) {
                if (($row->name ?? '') === $index) {
                    return true;
                }
            }

            return false;
        }

        try {
            $db = Schema::getConnection()->getDatabaseName();
            $rows = DB::select(
                'SELECT COUNT(*) AS c FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ?',
                [$db, $table, $index]
            );

            return ((int) ($rows[0]->c ?? 0)) > 0;
        } catch (Throwable) {
            return false;
        }
    }

    private function dropIndexIfExists(string $table, string $index): void
    {
        if (! $this->indexExists($table, $index)) {
            return;
        }
        try {
            Schema::table($table, function (Blueprint $blueprint) use ($index) {
                $blueprint->dropUnique($index);
            });
        } catch (Throwable) {
            try {
                Schema::table($table, function (Blueprint $blueprint) use ($index) {
                    $blueprint->dropIndex($index);
                });
            } catch (Throwable) {
            }
        }
    }
};
