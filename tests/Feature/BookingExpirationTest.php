<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\BookingAuditLog;
use App\Models\BookingTraveler;
use App\Models\Branch;
use App\Models\Schedule;
use App\Models\Trip;
use App\Services\BookingExpirationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingExpirationTest extends TestCase
{
    use RefreshDatabase;

    private Trip $trip;
    private Schedule $schedule;
    private Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->trip = Trip::create([
            'title'               => 'Expiry Test Trip',
            'slug'                => 'expiry-test',
            'description'         => 'test',
            'is_domestic'         => true,
            'destination_country' => 'Indonesia',
            'price'               => 3_000_000,
            'duration_days'       => 3,
        ]);

        $this->schedule = Schedule::create([
            'trip_id'        => $this->trip->id,
            'departure_date' => now()->addDays(15),
            'return_date'    => now()->addDays(18),
            'quota'          => 20,
        ]);

        $this->branch = Branch::create([
            'name' => 'Test Branch', 'is_active' => true,
        ]);
    }

    private function makeBooking(string $status = 'pending', int $minutesOld = 0): Booking
    {
        $booking = Booking::create([
            'schedule_id'   => $this->schedule->id,
            'branch_id'     => $this->branch->id,
            'customer_name' => 'Test User ' . now()->timestamp,
            'customer_email'=> 'test' . rand() . '@test.com',
            'customer_phone'=> '081234567890',
            'pax'           => 1,
            'total_amount'  => 3_000_000,
            'status'        => $status,
        ]);

        BookingTraveler::create([
            'booking_id' => $booking->id,
            'name'       => $booking->customer_name,
            'email'      => $booking->customer_email,
            'phone'      => $booking->customer_phone,
            'is_primary' => true,
            'status'     => $status,
        ]);

        if ($minutesOld > 0) {
            // Backdate created_at
            $booking->update(['created_at' => now()->subMinutes($minutesOld)]);
        }

        return $booking->refresh();
    }

    // ─── CORE EXPIRY TESTS ────────────────────────────────────────────────

    /** Booking < 1 jam should NOT be expired */
    public function test_fresh_pending_booking_is_not_expired(): void
    {
        $booking = $this->makeBooking('pending', 30); // 30 minutes old
        (new BookingExpirationService(60))->run('system');

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id, 'status' => 'pending',
        ]);
        $this->assertEquals(0, BookingAuditLog::where('booking_id', $booking->id)->count());
    }

    /** Booking > 1 jam should be auto-cancelled */
    public function test_old_pending_booking_is_auto_cancelled(): void
    {
        $booking = $this->makeBooking('pending', 90); // 90 minutes old
        $result  = (new BookingExpirationService(60))->run('system');

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id, 'status' => 'cancelled',
        ]);
        $this->assertEquals(1, $result['expired_count']);
    }

    /** PAID booking must never be touched */
    public function test_paid_booking_is_never_expired(): void
    {
        $booking = $this->makeBooking('paid', 120); // 2 hours old, already paid
        (new BookingExpirationService(60))->run('system');

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id, 'status' => 'paid',
        ]);
    }

    /** Already CANCELLED booking must not be re-processed */
    public function test_already_cancelled_booking_is_not_re_processed(): void
    {
        $booking  = $this->makeBooking('cancelled', 120);
        $logsBefore = BookingAuditLog::count();
        (new BookingExpirationService(60))->run('system');

        $this->assertEquals($logsBefore, BookingAuditLog::count(), 'No new log for already-cancelled booking');
    }

    /** Bulk expiry handles multiple bookings atomically */
    public function test_bulk_expiry_handles_multiple_bookings_atomically(): void
    {
        $b1 = $this->makeBooking('pending', 65); // expired
        $b2 = $this->makeBooking('pending', 75); // expired
        $b3 = $this->makeBooking('pending', 30); // NOT expired
        $b4 = $this->makeBooking('paid',   120); // PAID — untouched

        $result = (new BookingExpirationService(60))->run('system');

        $this->assertEquals(2, $result['expired_count']);
        $this->assertDatabaseHas('bookings', ['id' => $b1->id, 'status' => 'cancelled']);
        $this->assertDatabaseHas('bookings', ['id' => $b2->id, 'status' => 'cancelled']);
        $this->assertDatabaseHas('bookings', ['id' => $b3->id, 'status' => 'pending']);
        $this->assertDatabaseHas('bookings', ['id' => $b4->id, 'status' => 'paid']);
    }

    // ─── AUDIT LOG TESTS ──────────────────────────────────────────────────

    /** Audit log is written for each expired booking */
    public function test_audit_log_is_written_for_each_expired_booking(): void
    {
        $b1 = $this->makeBooking('pending', 70);
        $b2 = $this->makeBooking('pending', 80);
        (new BookingExpirationService(60))->run('admin:SuperAdmin');

        $logs = BookingAuditLog::where('action', 'auto_expired')->get();

        $this->assertCount(2, $logs);
        $this->assertTrue($logs->every(fn($l) => $l->old_status === 'pending'));
        $this->assertTrue($logs->every(fn($l) => $l->new_status === 'cancelled'));
        $this->assertTrue($logs->every(fn($l) => $l->triggered_by === 'admin:SuperAdmin'));
    }

    /** consecutive runs don't create duplicate log entries */
    public function test_consecutive_runs_do_not_create_duplicate_logs(): void
    {
        $this->makeBooking('pending', 90);

        (new BookingExpirationService(60))->run('system'); // first run — expires it
        (new BookingExpirationService(60))->run('system'); // second run — nothing left to expire

        $this->assertEquals(1, BookingAuditLog::where('action', 'auto_expired')->count());
    }

    /** Travelers are also cancelled when booking expires */
    public function test_travelers_are_cancelled_when_booking_expires(): void
    {
        $booking = $this->makeBooking('pending', 120);

        // Add second traveler
        BookingTraveler::create([
            'booking_id' => $booking->id,
            'name'       => 'Traveler 2',
            'email'      => 't2@test.com',
            'phone'      => '089999',
            'is_primary' => false,
            'status'     => 'pending',
        ]);

        // Manually bump pax to 2
        $booking->update(['pax' => 2]);

        (new BookingExpirationService(60))->run('system');

        $booking->refresh();
        $this->assertEquals('cancelled', $booking->status);
        $this->assertEquals(0, $booking->travelers()->where('status', '!=', 'cancelled')->count());
    }

    /** Empty result when no bookings match */
    public function test_returns_zero_when_no_expired_bookings(): void
    {
        $this->makeBooking('pending', 10); // recent — should NOT expire
        $result = (new BookingExpirationService(60))->run('system');

        $this->assertEquals(0, $result['expired_count']);
        $this->assertEquals(0, BookingAuditLog::count());
    }
}
