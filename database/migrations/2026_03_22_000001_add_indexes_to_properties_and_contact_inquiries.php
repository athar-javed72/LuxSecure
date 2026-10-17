<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->index('is_active');
            $table->index('type');
            $table->index('price');
            $table->index('location');
            $table->index(['is_active', 'type', 'price'], 'properties_active_type_price_index');
        });

        Schema::table('contact_inquiries', function (Blueprint $table) {
            $table->index('is_read');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['type']);
            $table->dropIndex(['price']);
            $table->dropIndex(['location']);
            $table->dropIndex('properties_active_type_price_index');
        });

        Schema::table('contact_inquiries', function (Blueprint $table) {
            $table->dropIndex(['is_read']);
            $table->dropIndex(['created_at']);
        });
    }
};
