<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripLocation extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function itineraries()
    {
        return $this->hasMany(TripItinerary::class)->orderBy('sort_order')->orderBy('day');
    }

    public function accommodations()
    {
        return $this->hasMany(TripAccommodation::class);
    }
}
