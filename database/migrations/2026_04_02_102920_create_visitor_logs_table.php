<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitor_logs', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->nullable()->index();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            
            // Device & Browser info
            $table->string('device_type')->nullable(); // mobile, desktop, tablet
            $table->string('platform')->nullable();    // Windows, iOS, Android
            $table->string('browser')->nullable();     // Chrome, Safari
            
            // Geography
            $table->string('country')->nullable();
            $table->string('region')->nullable();
            $table->string('city')->nullable();
            
            // Source info
            $table->string('referrer')->nullable();
            $table->string('source')->nullable(); // Organic, Direct, Social, etc.
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            
            $table->timestamps();
        });

        Schema::create('visitor_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visitor_log_id')->constrained('visitor_logs')->onDelete('cascade');
            $table->string('event_type'); // page_view, cta_click, booking_start, payment_success
            $table->string('event_name')->nullable(); // slug or button id
            $table->text('url')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index('event_type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitor_events');
        Schema::dropIfExists('visitor_logs');
    }
};
