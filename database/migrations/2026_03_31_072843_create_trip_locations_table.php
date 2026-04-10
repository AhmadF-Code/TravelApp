<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained()->cascadeOnDelete();
            $table->string('country');         // Nama negara/kota, mis: "Thailand"
            $table->string('city')->nullable(); // Kota spesifik, mis: "Bangkok, Phuket"
            $table->string('flag_emoji', 10)->nullable(); // mis: 🇹🇭
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_locations');
    }
};
