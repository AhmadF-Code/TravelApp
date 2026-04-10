<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingTraveler extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($traveler) {
            if (empty($traveler->traveler_code)) {
                $traveler->traveler_code = 'TKT-' . strtoupper(\Illuminate\Support\Str::random(8));
            }
        });

        static::updated(function ($traveler) {
            // New logic: If primary is cancelled, transfer leadership if possible
            if ($traveler->is_primary && $traveler->status === 'cancelled' && $traveler->wasChanged('status')) {
                $booking = $traveler->booking;
                if ($booking) {
                    $nextLeader = $booking->travelers()
                        ->where('status', '!=', 'cancelled')
                        ->where('id', '!=', $traveler->id)
                        ->first();

                    if ($nextLeader) {
                        // Unset current primary (he is already cancelled, but just to be sure)
                        $traveler->updateQuietly(['is_primary' => false]);
                        
                        // Set new leader
                        $nextLeader->updateQuietly(['is_primary' => true]);

                        // Update Booking Header to the new leader
                        $booking->update([
                            'customer_name'  => $nextLeader->name,
                            'customer_email' => $nextLeader->email,
                            'customer_phone' => $nextLeader->phone,
                            // Keep 'paid' or 'pending' status
                        ]);
                    } else {
                        // No one else is active. Cancel the whole group.
                        if ($booking->status !== 'cancelled') {
                             $booking->update(['status' => 'cancelled']);
                        }
                    }
                }
            }
        });
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
