<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\BookingTraveler;

class SyncBookingsToParticipants extends Command
{
    protected $signature = 'sync:bookings-to-participants';
    protected $description = 'Sync existing bookings without travelers records to the BookingTraveler table.';

    public function handle()
    {
        $bookings = Booking::all();
        $synced = 0;
        $createdCount = 0;

        foreach ($bookings as $booking) {
            $existing = $booking->travelers()->count();
            
            // If we have fewer travelers than pax, we need to populate
            if ($existing < $booking->pax) {
                // 1. Check/Create Primary
                $primary = $booking->travelers()->where('is_primary', true)->first();
                if (!$primary) {
                    $booking->travelers()->create([
                        'name' => $booking->customer_name,
                        'email' => $booking->customer_email,
                        'phone' => $booking->customer_phone,
                        'passport_number' => $booking->primary_passport,
                        'is_primary' => true,
                        'status' => $booking->status,
                    ]);
                    $createdCount++;
                }

                // 2. Check/Create Additional from JSON
                $additionalJson = $booking->additional_travelers;
                if ($additionalJson && is_iterable($additionalJson)) {
                    foreach ($additionalJson as $index => $traveler) {
                        // Check if this traveler already exists by name/email (simplistic)
                        // but better: just check count
                        if ($booking->travelers()->count() < $booking->pax) {
                            $booking->travelers()->create([
                                'name' => $traveler['name'] ?? ('Traveler ' . ($index + 1)),
                                'email' => $traveler['email'] ?? null,
                                'phone' => $traveler['phone'] ?? null,
                                'passport_number' => $traveler['passport'] ?? null,
                                'is_primary' => false,
                                'status' => $booking->status,
                            ]);
                            $createdCount++;
                        }
                    }
                }
                
                // 3. Fill remaining gaps up to pax if they exist
                while ($booking->travelers()->count() < $booking->pax) {
                    $idx = $booking->travelers()->count() + 1;
                    $booking->travelers()->create([
                        'name' => 'Participant ' . $idx,
                        'email' => $booking->customer_email,
                        'phone' => $booking->customer_phone,
                        'is_primary' => false,
                        'status' => $booking->status,
                    ]);
                    $createdCount++;
                }

                $synced++;
            }
        }

        $this->info("Sync completed! Processed {$synced} bookings and created {$createdCount} traveler records.");
    }
}
