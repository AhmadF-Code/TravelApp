<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_itineraries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_location_id')->constrained('trip_locations')->cascadeOnDelete();
            $table->unsignedSmallInteger('day');       // Hari ke-berapa (1, 2, 3...)
            $table->string('title');                   // mis: "Tiba di Bangkok & Street Food Tour"
            $table->text('description')->nullable();   // Deskripsi detail kegiatan
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_itineraries');
    }
};
