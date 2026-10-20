<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_assets', function (Blueprint $table) {
            $table->id();
            $table->string('slot', 64)->index();
            $table->string('type', 16); // image|video|embed
            $table->string('disk', 32)->default('public');
            $table->string('path')->nullable();
            $table->string('poster_path')->nullable();
            $table->string('embed_provider', 16)->nullable(); // youtube|vimeo
            $table->string('embed_id', 64)->nullable();
            $table->string('alt')->nullable();
            $table->boolean('decorative')->default(false);
            $table->string('caption')->nullable();
            $table->unsignedTinyInteger('focal_x')->default(50);
            $table->unsignedTinyInteger('focal_y')->default(50);
            $table->unsignedSmallInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_demo')->default(false);
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('content_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('key', 96)->index();
            $table->string('locale', 12)->default('en');
            $table->string('title')->nullable();
            $table->text('body')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->unique(['key', 'locale']);
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->string('cover_path')->nullable()->after('population');
            $table->unsignedTinyInteger('cover_focal_x')->default(50)->after('cover_path');
            $table->unsignedTinyInteger('cover_focal_y')->default(50)->after('cover_focal_x');
        });

        Schema::table('faqs', function (Blueprint $table) {
            $table->string('tag', 32)->nullable()->after('is_published')->index();
        });
    }

    public function down(): void
    {
        Schema::table('faqs', function (Blueprint $table) {
            $table->dropColumn('tag');
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn(['cover_path', 'cover_focal_x', 'cover_focal_y']);
        });

        Schema::dropIfExists('content_blocks');
        Schema::dropIfExists('media_assets');
    }
};
