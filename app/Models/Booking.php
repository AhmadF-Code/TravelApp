<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'additional_travelers' => 'array',
        'discount_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'refund_completed_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($booking) {
            if (empty($booking->booking_code)) {
                $booking->booking_code = 'TRV-' . strtoupper(\Illuminate\Support\Str::random(6));
            }
        });

        static::updated(function ($booking) {
            if ($booking->wasChanged('status')) {
                // Sync status to all travelers who are NOT cancelled
                // (except if the booking itself is being cancelled, then cancel all)
                if ($booking->status === 'paid') {
                    $booking->travelers()
                        ->where('status', '!=', 'cancelled')
                        ->update(['status' => 'paid']);
                } elseif ($booking->status === 'cancelled') {
                    $booking->travelers()->update(['status' => 'cancelled']);
                }
            }
        });
    }

    public function travelers()
    {
        return $this->hasMany(BookingTraveler::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function getTripAttribute()
    {
        return $this->schedule->trip ?? null;
    }
    public function refundProcessor()
    {
        return $this->belongsTo(User::class, 'refund_processed_by_id');
    }
}
