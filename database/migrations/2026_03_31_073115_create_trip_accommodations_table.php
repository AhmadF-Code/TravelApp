<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_accommodations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_location_id')->constrained('trip_locations')->cascadeOnDelete();
            $table->string('name');              // Nama hotel/penginapan
            $table->string('type')->nullable();  // mis: "Bintang 4", "Resort", "Hostel"
            $table->string('notes')->nullable(); // Catatan tambahan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_accommodations');
    }
};
