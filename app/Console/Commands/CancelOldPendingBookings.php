<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;

class CancelOldPendingBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:cancel-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Batalkan pesanan pending yang sudah lewat dari 1 jam sejak dibuat';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = Booking::where('status', 'pending')
            ->where('created_at', '<=', now()->subHour())
            ->update(['status' => 'cancelled']);

        if ($count > 0) {
            $this->info("Berhasil membatalkan {$count} pesanan pending yang kadaluarsa.");
        } else {
            $this->info("Tidak ada pesanan pending yang harus dibatalkan.");
        }
    }
}
