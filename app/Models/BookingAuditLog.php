<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingAuditLog extends Model
{
    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Quick helper to record a log entry anywhere in the app.
     */
    public static function record(
        string  $action,
        ?int    $bookingId    = null,
        ?string $bookingCode  = null,
        ?string $oldStatus    = null,
        ?string $newStatus    = null,
        ?float  $amount       = null,
        string  $triggeredBy  = 'system',
        ?string $notes        = null,
    ): void {
        static::create([
            'booking_id'   => $bookingId,
            'booking_code' => $bookingCode,
            'action'       => $action,
            'old_status'   => $oldStatus,
            'new_status'   => $newStatus,
            'amount'       => $amount,
            'triggered_by' => $triggeredBy,
            'notes'        => $notes,
            'ip_address'   => request()->ip(),
        ]);
    }
}
