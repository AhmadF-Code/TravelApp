<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function bookings()
    {
        return $this->hasManyThrough(Booking::class, Schedule::class);
    }

    public function locations()
    {
        return $this->hasMany(TripLocation::class)->orderBy('sort_order');
    }

    public function getImageUrlAttribute()
    {
        if (empty($this->image)) {
            return 'https://via.placeholder.com/800x400';
        }

        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        return asset('storage/' . $this->image);
    }
}
