<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingAuditLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * BookingExpirationService
 * ─────────────────────────────────────────────────────────────────────────
 * Central business-logic service for auto-expiring stale PENDING bookings.
 *
 * Rules:
 *   - Only targets bookings with status = 'pending'
 *   - Booking must be older than $expiryMinutes (default 60 min)
 *   - Does NOT touch PAID or CANCELLED bookings
 *   - Uses DB-level bulk update to avoid N+1 and race conditions
 *   - Writes one audit log entry per expired booking
 *   - Returns a summary array for display in the admin panel
 */
class BookingExpirationService
{
    /**
     * Expiry threshold in minutes.
     */
    private int $expiryMinutes;

    public function __construct(int $expiryMinutes = 60)
    {
        $this->expiryMinutes = $expiryMinutes;
    }

    /**
     * Run expiration check.
     *
     * @return array{expired_count: int, checked_at: string, next_expiry: ?string}
     */
    public function run(string $triggeredBy = 'system'): array
    {
        $cutoff = Carbon::now()->subMinutes($this->expiryMinutes);

        // 1. Find all candidates first (for audit logging) — lightweight select
        $expiredBookings = Booking::query()
            ->where('status', 'pending')
            ->where('bookings.created_at', '<', $cutoff)
            ->select(['id', 'booking_code', 'total_amount', 'status'])
            ->get();

        if ($expiredBookings->isEmpty()) {
            return [
                'expired_count' => 0,
                'checked_at'    => Carbon::now()->toISOString(),
                'next_expiry'   => $this->getNextExpiryTime(),
            ];
        }

        $ids = $expiredBookings->pluck('id')->toArray();

        // 2. Bulk update in a single DB call (prevents race conditions)
        DB::transaction(function () use ($ids, $expiredBookings, $cutoff, $triggeredBy) {
            // Update all travelers of expired bookings
            DB::table('booking_travelers')
                ->whereIn('booking_id', $ids)
                ->where('status', 'pending')
                ->update([
                    'status'     => 'cancelled',
                    'updated_at' => Carbon::now(),
                ]);

            // Update bookings themselves
            DB::table('bookings')
                ->whereIn('id', $ids)
                ->update([
                    'status'         => 'cancelled',
                    'follow_up_note' => 'Expired otomatis: pembayaran tidak diselesaikan dalam ' . $this->expiryMinutes . ' menit.',
                    'updated_at'     => Carbon::now(),
                ]);

            // Write audit log for each
            $now  = Carbon::now();
            $logs = $expiredBookings->map(fn($b) => [
                'booking_id'   => $b->id,
                'booking_code' => $b->booking_code,
                'action'       => 'auto_expired',
                'old_status'   => 'pending',
                'new_status'   => 'cancelled',
                'amount'       => $b->total_amount,
                'triggered_by' => $triggeredBy,
                'notes'        => "Booking tidak dibayar dalam {$this->expiryMinutes} menit sejak pembuatan. Cut-off: {$cutoff->toDateTimeString()}",
                'ip_address'   => request()->ip(),
                'created_at'   => $now,
                'updated_at'   => $now,
            ])->toArray();

            DB::table('booking_audit_logs')->insert($logs);
        });

        // 3. System log too
        Log::info('[BookingExpiration] Auto-expired ' . $expiredBookings->count() . ' bookings.', [
            'ids'          => $ids,
            'triggered_by' => $triggeredBy,
            'cutoff'       => $cutoff->toDateTimeString(),
        ]);

        return [
            'expired_count' => $expiredBookings->count(),
            'checked_at'    => Carbon::now()->toISOString(),
            'next_expiry'   => $this->getNextExpiryTime(),
        ];
    }

    /**
     * Get when the next oldest pending booking will expire.
     */
    private function getNextExpiryTime(): ?string
    {
        $next = Booking::where('status', 'pending')
            ->orderBy('created_at')
            ->value('created_at');

        if (!$next) return null;

        return Carbon::parse($next)->addMinutes($this->expiryMinutes)->toISOString();
    }
}
