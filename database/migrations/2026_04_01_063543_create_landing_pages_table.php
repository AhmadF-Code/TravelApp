<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('landing_pages', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Internal name for staging list
            $table->boolean('is_active')->default(false); // Staging status (publish / not published)
            
            // Meta & SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('gtm_id')->nullable();

            // Hero Section
            $table->string('hero_title')->nullable();
            $table->string('hero_subtitle')->nullable();
            $table->text('hero_text')->nullable();
            $table->string('hero_background_image')->nullable();
            
            // Produk Unggulan
            $table->string('featured_trip_title')->nullable();
            $table->string('featured_trip_subtitle')->nullable();

            // About Section
            $table->string('about_title')->nullable();
            $table->string('about_subtitle')->nullable();
            $table->text('about_text')->nullable();
            $table->string('about_image')->nullable();

            // Video & Foto Kegiatan
            $table->string('youtube_url')->nullable();
            $table->json('gallery_images')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_pages');
    }
};
