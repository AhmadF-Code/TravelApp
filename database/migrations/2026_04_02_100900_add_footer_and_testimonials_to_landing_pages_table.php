<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            // Testimonials
            $table->string('testimonial_title')->nullable()->after('youtube_url');
            $table->json('testimonials')->nullable()->after('testimonial_title');

            // CTA Section
            $table->string('cta_title')->nullable()->after('testimonials');
            $table->string('cta_subtitle')->nullable()->after('cta_title');

            // Footer Section
            $table->text('footer_text')->nullable()->after('cta_subtitle');
            $table->string('footer_email', 100)->nullable()->after('footer_text');
            $table->string('footer_phone', 50)->nullable()->after('footer_email');
            $table->string('footer_address')->nullable()->after('footer_phone');
        });
    }

    public function down(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            $table->dropColumn([
                'testimonial_title',
                'testimonials',
                'cta_title',
                'cta_subtitle',
                'footer_text',
                'footer_email',
                'footer_phone',
                'footer_address',
            ]);
        });
    }
};
