<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Schedule;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityEnhancementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function security_headers_are_present()
    {
        $response = $this->get('/');

        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    /** @test */
    public function honeypot_field_prevents_submission()
    {
        $trip = Trip::create([
            'title' => 'Test Trip',
            'slug' => 'test-trip',
            'price' => 1000000,
            'description' => 'Test',
            'is_domestic' => true
        ]);
        
        $branch = Branch::create(['name' => 'Test Branch', 'is_active' => true]);
        
        $schedule = Schedule::create([
            'trip_id' => $trip->id,
            'departure_date' => now()->addDays(10),
            'return_date' => now()->addDays(15),
            'quota' => 10,
            'status' => 'scheduled'
        ]);

        $response = $this->post(route('trip.book', 'test-trip'), [
            'website_url' => 'http://evil-bot.com', // Honeypot filled
            'customer_name' => 'Bot',
            'customer_email' => 'bot@bot.com',
            'customer_phone' => '08123456789',
            'pax' => 1,
            'branch_id' => $branch->id,
            'schedule_id' => $schedule->id,
            'form_timestamp' => time(),
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseEmpty('bookings');
    }

    /** @test */
    public function fast_submission_prevents_booking()
    {
        $trip = Trip::create([
            'title' => 'Test Trip Fast',
            'slug' => 'test-trip-fast',
            'price' => 1000000,
            'description' => 'Test',
            'is_domestic' => true
        ]);
        
        $branch = Branch::create(['name' => 'Test Branch Fast', 'is_active' => true]);
        
        $schedule = Schedule::create([
            'trip_id' => $trip->id,
            'departure_date' => now()->addDays(10),
            'return_date' => now()->addDays(15),
            'quota' => 10,
            'status' => 'scheduled'
        ]);

        $response = $this->post(route('trip.book', 'test-trip-fast'), [
            'website_url' => '', // Honeypot empty
            'customer_name' => 'Human',
            'customer_email' => 'human@test.com',
            'customer_phone' => '08123456789',
            'pax' => 1,
            'branch_id' => $branch->id,
            'schedule_id' => $schedule->id,
            'form_timestamp' => time(), // Too fast (same second)
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseEmpty('bookings');
    }

    /** @test */
    public function deactivated_user_cannot_access_admin_panel()
    {
        $user = User::factory()->create([
            'is_active' => false,
        ]);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(403);
    }

    /** @test */
    public function active_user_can_access_admin_panel()
    {
        $user = User::factory()->create([
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->followingRedirects()->get('/admin/');

        $response->assertStatus(200);
    }

    /** @test */
    public function strip_tags_whitelist_works_in_welcome_page()
    {
        // We can't easily test the rendered view logic without a complex E2E test, 
        // but we can verify the controller logic or just simple string check if needed.
        // For now, let's just check if the home page loads fine with these tags.
        
        $landingPage = \App\Models\LandingPage::create([
            'title' => 'Test Landing Page',
            'hero_text' => 'Hello <script>alert("XSS")</script> <span class="bg-red-500">Test</span>',
            'is_active' => true,
        ]);

        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertDontSee('<script>alert("XSS")</script>', false);
        $response->assertSee('<span class="bg-red-500">Test</span>', false);
    }

    /** @test */
    public function sql_injection_attempt_is_properly_sanitized()
    {
        $trip = \App\Models\Trip::create(['title'=>'T','slug'=>'t','price'=>1,'description'=>'D']);
        $branch = \App\Models\Branch::create(['name'=>'B', 'is_active'=>true]);
        $schedule = \App\Models\Schedule::create(['trip_id'=>$trip->id, 'departure_date'=>now(), 'return_date'=>now()]);

        // Create an actual booking to ensure we don't accidentally get it
        $booking = \App\Models\Booking::create([
            'booking_code' => 'SAFE123',
            'customer_name' => 'Real User',
            'customer_email' => 'real@test.com',
            'customer_phone' => '0812345678',
            'schedule_id' => $schedule->id,
            'branch_id' => $branch->id,
            'total_amount' => 1000,
            'status' => 'paid'
        ]);

        // Attempt SQL injection via the order search route
        // We use a classic payload: ' OR 1=1 --
        $response = $this->followingRedirects()->post(route('booking.search'), [
            'code' => "' OR 1=1 --",
            'email' => 'anything@test.com',
            'traveler_code' => 'anything'
        ]);

        // It should NOT find a booking and should NOT redirect to a success page
        $response->assertSee('Kombinasi Kode Booking, Email, dan ID Kursi tidak ditemukan.');
        $this->assertFalse(session()->has('auth_booking_SAFE123'));
    }
}
