<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Booking;
use App\Mail\BookingPaidMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Midtrans\Config;
use Midtrans\Transaction;

class RefreshBookingStatusesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Only check 5 most recent pending bookings to keep it extremely fast
        $pendingBookings = Booking::where('status', 'pending')
            ->where('created_at', '>=', now()->subDays(2))
            ->latest()
            ->take(5)
            ->get();

        if ($pendingBookings->isEmpty()) {
            return;
        }

        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');

        foreach ($pendingBookings as $booking) {
            // Check manual expiry (> 1 hour)
            if ($booking->created_at->addHour()->isPast()) {
                $booking->update(['status' => 'cancelled']);
                if ($booking->schedule) {
                    $booking->schedule->increment('quota', $booking->pax);
                }
                Log::info("Booking {$booking->id} auto-cancelled via Job (timeout >1h).");
                continue;
            }

            // Check Midtrans if available
            if ($booking->midtrans_order_id) {
                try {
                    $status = Transaction::status($booking->midtrans_order_id);
                    $mStatus = $status->transaction_status;

                    if ($mStatus === 'capture' || $mStatus === 'settlement') {
                        $booking->update(['status' => 'paid']);
                        try {
                            Mail::to($booking->customer_email)->send(new BookingPaidMail($booking));
                        } catch (\Exception $e) {
                            Log::error("Paid Email failed for booking {$booking->id}: " . $e->getMessage());
                        }
                    } elseif ($mStatus === 'deny' || $mStatus === 'expire' || $mStatus === 'cancel') {
                        $booking->update(['status' => 'cancelled']);
                        if ($booking->schedule) {
                            $booking->schedule->increment('quota', $booking->pax);
                        }
                    }
                } catch (\Exception $e) {
                    Log::error("Midtrans refresh failed via Job for booking {$booking->id}: " . $e->getMessage());
                }
            }
        }
    }
}
