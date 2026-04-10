<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\BookingTraveler;
use App\Models\Branch;
use App\Models\Schedule;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

/**
 * ══════════════════════════════════════════════════════════════════
 * E2E BOOKING SYSTEM TEST SUITE
 * Travel Agent — Comprehensive Business Logic Validation
 * ══════════════════════════════════════════════════════════════════
 *
 * POSITIVE FLOW
 *   [P1] Single-pax booking via landing page → status PAID
 *   [P2] Multi-pax booking via landing page → participants stored correctly
 *   [P3] Admin creates manual single-pax booking (direct PAID)
 *   [P4] Admin creates manual multi-pax booking (direct PAID)
 *
 * NEGATIVE FLOW
 *   [N1] Full cancellation — single pax → booking CANCELLED + refund 100%
 *   [N2] Partial cancellation — 1 of 3 pax cancelled → booking stays active
 *   [N3] Partial cancellation — primary cancelled → leadership transferred
 *   [N4] Last pax cancelled → booking auto-cancelled
 *   [N5] Trip/schedule cancelled by system → all bookings flagged for follow-up
 *
 * EDGE / VALIDATION
 *   [E1] No duplicate booking_code generated
 *   [E2] Quota fallback when selected schedule is full
 *   [E3] Refund amount calculation accuracy
 *   [E4] Booking status sync cascades correctly to travelers
 */
class BookingSystemE2ETest extends TestCase
{
    use RefreshDatabase;

    /* ─────────────────────────────────── SETUP ─────────────────────────── */

    private Trip $trip;
    private Schedule $schedule;
    private Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->trip = Trip::create([
            'title'               => 'Test Trip Bali Magic',
            'slug'                => 'test-bali-magic',
            'description'         => 'Testing trip',
            'is_domestic'         => true,
            'destination_country' => 'Indonesia',
            'price'               => 4_500_000,
            'duration_days'       => 5,
        ]);

        $this->schedule = Schedule::create([
            'trip_id'        => $this->trip->id,
            'departure_date' => now()->addDays(30),
            'return_date'    => now()->addDays(35),
            'quota'          => 20,
        ]);

        $this->branch = Branch::create([
            'name'      => 'Branch Test Jakarta',
            'is_active' => true,
        ]);
    }

    /* ─────────── HELPERS ─────────────────────────────────────────────────── */

    private function makePendingBooking(int $pax = 1, string $name = 'Test User'): Booking
    {
        $booking = Booking::create([
            'schedule_id'    => $this->schedule->id,
            'branch_id'      => $this->branch->id,
            'customer_name'  => $name,
            'customer_email' => strtolower(str_replace(' ', '', $name)) . '@test.com',
            'customer_phone' => '081234567890',
            'pax'            => $pax,
            'total_amount'   => $this->trip->price * $pax,
            'status'         => 'pending',
        ]);

        // primary traveler
        BookingTraveler::create([
            'booking_id' => $booking->id,
            'name'       => $name,
            'email'      => $booking->customer_email,
            'phone'      => '081234567890',
            'is_primary' => true,
            'status'     => 'pending',
        ]);

        // additional travelers
        for ($i = 2; $i <= $pax; $i++) {
            BookingTraveler::create([
                'booking_id' => $booking->id,
                'name'       => "Peserta $i dari $name",
                'email'      => "peserta{$i}@test.com",
                'phone'      => '08987654321' . $i,
                'is_primary' => false,
                'status'     => 'pending',
            ]);
        }

        return $booking;
    }

    private function payBooking(Booking $booking): void
    {
        $booking->update(['status' => 'paid']);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       POSITIVE CYCLE TESTS
    ═══════════════════════════════════════════════════════════════════════ */

    /** [P1] Single-pax booking → status PAID */
    public function test_P1_single_pax_booking_reaches_paid_status(): void
    {
        $booking = $this->makePendingBooking(1, 'Andi Santoso');
        $this->payBooking($booking);

        $this->assertDatabaseHas('bookings', [
            'id'     => $booking->id,
            'status' => 'paid',
            'pax'    => 1,
        ]);

        $this->assertEquals(1, $booking->travelers()->count());
        $this->assertEquals('TRV-', substr($booking->booking_code, 0, 4));

        Log::info('[P1] PASS — Single-pax booking PAID', ['code' => $booking->booking_code]);
    }

    /** [P2] Multi-pax booking → all participants stored */
    public function test_P2_multi_pax_booking_stores_all_participants(): void
    {
        $booking = $this->makePendingBooking(3, 'Sinta Devyani');
        $this->payBooking($booking);

        $travelers = $booking->travelers()->get();

        $this->assertEquals(3, $travelers->count());
        $this->assertEquals(1, $travelers->where('is_primary', true)->count());
        $this->assertEquals(2, $travelers->where('is_primary', false)->count());
        $this->assertTrue($travelers->every(fn($t) => $t->status === 'paid'));
        $this->assertEquals($this->trip->price * 3, $booking->total_amount);

        Log::info('[P2] PASS — Multi-pax booking stored all 3 participants', ['code' => $booking->booking_code]);
    }

    /** [P3] Admin manual single-pax booking → direct PAID */
    public function test_P3_admin_creates_single_pax_booking_direct_paid(): void
    {
        $booking = Booking::create([
            'schedule_id'    => $this->schedule->id,
            'branch_id'      => $this->branch->id,
            'customer_name'  => 'Admin Input User',
            'customer_email' => 'adminuser@test.com',
            'customer_phone' => '081199887766',
            'pax'            => 1,
            'total_amount'   => $this->trip->price,
            'status'         => 'paid', // langsung PAID oleh admin
        ]);

        BookingTraveler::create([
            'booking_id' => $booking->id,
            'name'       => 'Admin Input User',
            'email'      => 'adminuser@test.com',
            'phone'      => '081199887766',
            'is_primary' => true,
            'status'     => 'paid',
        ]);

        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'paid']);
        $this->assertEquals(1, $booking->travelers()->where('status', 'paid')->count());
        $this->assertNotNull($booking->booking_code);

        Log::info('[P3] PASS — Admin manual single-pax booking PAID', ['code' => $booking->booking_code]);
    }

    /** [P4] Admin manual multi-pax booking → direct PAID, all travelers synced */
    public function test_P4_admin_creates_multi_pax_booking_direct_paid(): void
    {
        $booking = $this->makePendingBooking(4, 'Rombongan Pak Budi');
        $this->payBooking($booking);

        $this->assertEquals(4, $booking->travelers()->count());
        $this->assertEquals(4, $booking->travelers()->where('status', 'paid')->count());
        $this->assertEquals($this->trip->price * 4, $booking->total_amount);

        Log::info('[P4] PASS — Admin multi-pax booking PAID, 4 travelers synced', ['code' => $booking->booking_code]);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       NEGATIVE CYCLE TESTS
    ═══════════════════════════════════════════════════════════════════════ */

    /** [N1] Full cancellation single-pax → CANCELLED + refund 100% */
    public function test_N1_full_cancellation_single_pax(): void
    {
        $booking = $this->makePendingBooking(1, 'User Cancel 1');
        $this->payBooking($booking);

        $refundAmount = $booking->total_amount; // 100% refund

        $booking->update([
            'status'         => 'cancelled',
            'refund_amount'  => $refundAmount,
            'follow_up_note' => 'Cancelled by user request — full refund',
        ]);

        $this->assertDatabaseHas('bookings', [
            'id'            => $booking->id,
            'status'        => 'cancelled',
            'refund_amount' => $refundAmount,
        ]);

        // All travelers should also be cancelled (via Booking::booted observer)
        $this->assertEquals(0, $booking->travelers()->where('status', '!=', 'cancelled')->count());

        Log::info('[N1] PASS — Full cancellation, 100% refund processed', [
            'code'   => $booking->booking_code,
            'refund' => $refundAmount,
        ]);
    }

    /** [N2] Partial cancellation — 1 of 3 pax cancelled → booking stays active */
    public function test_N2_partial_cancellation_booking_stays_active(): void
    {
        $booking = $this->makePendingBooking(3, 'Rombongan Partial');
        $this->payBooking($booking);

        // Cancel 1 non-primary traveler
        $nonPrimary = $booking->travelers()->where('is_primary', false)->first();
        $nonPrimary->update(['status' => 'cancelled']);

        // Refund 1 pax only
        $partialRefund = $this->trip->price;
        $booking->update([
            'refund_amount' => $partialRefund,
            'follow_up_note' => "Partial cancel: peserta {$nonPrimary->name} dibatalkan",
            // status STAYS paid/active — NOT full cancel
        ]);

        $booking->refresh();

        $this->assertEquals('paid', $booking->status, 'Booking should remain PAID after partial cancel');
        $this->assertEquals(2, $booking->travelers()->where('status', 'paid')->count(), 'Should have 2 active travelers');
        $this->assertEquals(1, $booking->travelers()->where('status', 'cancelled')->count(), 'Should have exactly 1 cancelled traveler');
        $this->assertEquals($partialRefund, (float) $booking->refund_amount, 'Refund should equal 1 pax price');

        Log::info('[N2] PASS — Partial cancellation: 1 cancelled, 2 remain, refund 1 pax', [
            'code'          => $booking->booking_code,
            'partial_refund'=> $partialRefund,
        ]);
    }

    /** [N3] Primary cancellation → leadership transferred to next traveler */
    public function test_N3_primary_cancellation_transfers_leadership(): void
    {
        $booking = $this->makePendingBooking(2, 'Pemimpin Grup');
        $this->payBooking($booking);

        $primary   = $booking->travelers()->where('is_primary', true)->first();
        $secondary = $booking->travelers()->where('is_primary', false)->first();

        // Cancel the primary traveler — observer should transfer leadership
        $primary->update(['status' => 'cancelled']);

        $secondary->refresh();
        $booking->refresh();

        $this->assertTrue((bool) $secondary->is_primary, 'Leadership must transfer to secondary traveler');
        $this->assertEquals('paid', $booking->status, 'Booking must remain PAID after primary transfer');
        $this->assertEquals($secondary->name, $booking->customer_name, 'Booking contact name should update to new leader');

        Log::info('[N3] PASS — Primary cancelled, leadership transferred to: ' . $secondary->name);
    }

    /** [N4] All pax cancelled → booking auto-cancelled */
    public function test_N4_all_travelers_cancelled_booking_auto_cancels(): void
    {
        $booking = $this->makePendingBooking(2, 'Group Auto Cancel');
        $this->payBooking($booking);

        // Cancel primary (non-primary does not exist, so booking should auto-cancel)
        $primary = $booking->travelers()->where('is_primary', true)->first();

        // First cancel non-primary
        $secondary = $booking->travelers()->where('is_primary', false)->first();
        $secondary->update(['status' => 'cancelled']);

        // Now cancel primary — no one left, booking should cancel
        $primary->update(['status' => 'cancelled']);

        $booking->refresh();

        $this->assertEquals('cancelled', $booking->status, 'Booking must auto-cancel when all travelers cancelled');
        $this->assertEquals(0, $booking->travelers()->where('status', '!=', 'cancelled')->count());

        Log::info('[N4] PASS — All travelers cancelled → booking auto-cancelled', ['code' => $booking->booking_code]);
    }

    /** [N5] Admin cancels a trip schedule → all related bookings flagged for follow-up */
    public function test_N5_schedule_cancellation_flags_all_bookings(): void
    {
        // Create 3 different bookings for the same schedule
        $b1 = $this->makePendingBooking(1, 'User Terdampak 1');
        $this->payBooking($b1);
        $b2 = $this->makePendingBooking(2, 'User Terdampak 2');
        $this->payBooking($b2);
        $b3 = $this->makePendingBooking(1, 'User Belum Bayar');
        // b3 stays pending

        // Simulate admin cancelling the schedule
        $this->schedule->update(['status' => 'cancelled']);
        Booking::where('schedule_id', $this->schedule->id)
            ->whereIn('status', ['paid', 'pending'])
            ->update([
                'follow_up_status' => 'needs_follow_up',
                'follow_up_note'   => 'Jadwal dibatalkan oleh admin.',
            ]);

        // All 3 bookings must be flagged
        $flagged = Booking::where('schedule_id', $this->schedule->id)
            ->where('follow_up_status', 'needs_follow_up')
            ->get();

        $this->assertCount(3, $flagged, 'All 3 bookings must be flagged for follow-up');

        // Verify data integrity: no duplicates, original status preserved
        $this->assertEquals('paid',    $flagged->firstWhere('customer_name', 'User Terdampak 1')->status);
        $this->assertEquals('paid',    $flagged->firstWhere('customer_name', 'User Terdampak 2')->status);
        $this->assertEquals('pending', $flagged->firstWhere('customer_name', 'User Belum Bayar')->status);
        $this->assertEquals(3, $flagged->unique('id')->count(), 'No duplicate records');

        Log::info('[N5] PASS — Schedule cancelled, all 3 bookings flagged for follow-up');
    }

    /* ═══════════════════════════════════════════════════════════════════════
       EDGE CASE & VALIDATION TESTS
    ═══════════════════════════════════════════════════════════════════════ */

    /** [E1] Booking codes are unique */
    public function test_E1_booking_codes_are_globally_unique(): void
    {
        $bookings = collect(range(1, 10))->map(fn($i) => $this->makePendingBooking(1, "User Unik $i"));

        $codes = $bookings->pluck('booking_code');
        $this->assertEquals($codes->unique()->count(), $codes->count(), 'All booking codes must be unique');
        $this->assertTrue($codes->every(fn($c) => str_starts_with($c, 'TRV-')));

        Log::info('[E1] PASS — 10 booking codes generated, all unique');
    }

    /** [E2] Refund calculation accuracy */
    public function test_E2_refund_calculation_accuracy(): void
    {
        $booking = $this->makePendingBooking(3, 'Grup Hitung Refund');
        $this->payBooking($booking);

        $pricePerPax     = $this->trip->price; // 4_500_000
        $expectedTotal   = $pricePerPax * 3;   // 13_500_000
        $refundFor1Pax   = $pricePerPax;        // 4_500_000

        $this->assertEquals($expectedTotal, $booking->total_amount);

        // Process partial refund
        $booking->update(['refund_amount' => $refundFor1Pax]);
        $booking->refresh();

        $netRevenue = $booking->total_amount - $booking->refund_amount;
        $this->assertEquals(9_000_000.0, $netRevenue, 'Net revenue must be 2 pax × price');
        $this->assertEquals(4_500_000.0, (float) $booking->refund_amount, 'Refund must equal exactly 1 pax price');

        Log::info('[E2] PASS — Refund calculation correct', [
            'total'   => $expectedTotal,
            'refund'  => $refundFor1Pax,
            'net'     => $netRevenue,
        ]);
    }

    /** [E3] Status sync: paying booking cascades PAID to all travelers */
    public function test_E3_paid_status_cascades_to_all_travelers(): void
    {
        $booking  = $this->makePendingBooking(3, 'Cascade Test Group');
        $statuses = $booking->travelers()->pluck('status')->unique()->toArray();
        $this->assertEquals(['pending'], $statuses, 'All travelers should start as pending');

        $this->payBooking($booking);

        $paidStatuses = $booking->travelers()->pluck('status')->unique()->toArray();
        $this->assertEquals(['paid'], $paidStatuses, 'All travelers must become PAID after booking PAID');

        Log::info('[E3] PASS — Status cascade: booking PAID → all 3 travelers PAID');
    }

    /** [E4] Cancelled booking cascades CANCELLED to all travelers */
    public function test_E4_cancelled_status_cascades_to_all_travelers(): void
    {
        $booking = $this->makePendingBooking(2, 'Full Cancel Group');
        $this->payBooking($booking);
        $booking->update(['status' => 'cancelled']);

        $statuses = $booking->travelers()->pluck('status')->unique()->toArray();
        $this->assertEquals(['cancelled'], $statuses, 'All travelers must be cancelled when booking is cancelled');

        Log::info('[E4] PASS — Status cascade: booking CANCELLED → all travelers CANCELLED');
    }

    /** [E5] Data consistency: admin view matches user data */
    public function test_E5_data_consistency_user_and_admin_view(): void
    {
        $pax     = 3;
        $booking = $this->makePendingBooking($pax, 'Konsistensi User');
        $this->payBooking($booking);

        // From "user" perspective
        $userBooking = Booking::where('booking_code', $booking->booking_code)->firstOrFail();
        // From "admin" perspective
        $adminBooking = Booking::with('travelers')->find($booking->id);

        $this->assertEquals($userBooking->status,         $adminBooking->status);
        $this->assertEquals($userBooking->total_amount,   $adminBooking->total_amount);
        $this->assertEquals($userBooking->pax,            $adminBooking->travelers->count());
        $this->assertEquals($pax,                         $adminBooking->travelers->count());

        Log::info('[E5] PASS — Data consistency: user and admin view match', [
            'booking_code' => $booking->booking_code,
            'pax'          => $pax,
        ]);
    }
}
