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
            if (! Schema::hasColumn('countries', 'locales')) {
                $table->json('locales')->nullable()->after('default_locale');
            }
            if (! Schema::hasColumn('countries', 'tax_needs_review')) {
                $table->boolean('tax_needs_review')->default(false)->after('tax_percent');
            }
        });

        if (Schema::hasColumn('countries', 'tax_needs_review')) {
            DB::table('countries')->where('iso2', 'PK')->update(['tax_needs_review' => true]);
        }

        Schema::table('languages', function (Blueprint $table) {
            if (! Schema::hasColumn('languages', 'needs_native_review')) {
                $table->boolean('needs_native_review')->default(false)->after('is_rtl');
            }
        });

        Schema::table('cancellation_policies', function (Blueprint $table) {
            if (! Schema::hasColumn('cancellation_policies', 'applies_to')) {
                $table->string('applies_to', 32)->nullable()->after('slug');
            }
            if (! Schema::hasColumn('cancellation_policies', 'reference_date')) {
                $table->string('reference_date', 32)->nullable()->after('applies_to');
            }
        });
    }

    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            if (Schema::hasColumn('countries', 'locales')) {
                $table->dropColumn('locales');
            }
            if (Schema::hasColumn('countries', 'tax_needs_review')) {
                $table->dropColumn('tax_needs_review');
            }
        });

        Schema::table('languages', function (Blueprint $table) {
            if (Schema::hasColumn('languages', 'needs_native_review')) {
                $table->dropColumn('needs_native_review');
            }
        });

        Schema::table('cancellation_policies', function (Blueprint $table) {
            if (Schema::hasColumn('cancellation_policies', 'applies_to')) {
                $table->dropColumn('applies_to');
            }
            if (Schema::hasColumn('cancellation_policies', 'reference_date')) {
                $table->dropColumn('reference_date');
            }
        });
    }
};
