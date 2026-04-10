<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Trip;
use App\Models\TripLocation;
use App\Models\TripItinerary;
use App\Models\TripAccommodation;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Buat Akun Admin ────────────────────────────────────────────────
        \App\Models\User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
            ]
        );

        // ── Bali Magic Explorer (Domestik) ──────────────────────────────────
        $bali = Trip::create([
            'title'               => 'Bali Magic Explorer',
            'slug'                => 'bali-magic',
            'description'         => 'Rasakan keajaiban budaya, pura-pura eksotis, dan pantai tersembunyi Bali dalam perjalanan 5 hari yang tak terlupakan.',
            'is_domestic'         => true,
            'destination_country' => 'Indonesia (Bali)',
            'price'               => 4500000,
            'duration_days'       => 5,
            'image'               => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?q=80&w=1938&auto=format&fit=crop',
        ]);

        $baliLoc = TripLocation::create(['trip_id' => $bali->id, 'country' => 'Indonesia', 'city' => 'Bali', 'flag_emoji' => '🇮🇩', 'sort_order' => 1]);
        TripItinerary::insert([
            ['trip_location_id' => $baliLoc->id, 'day' => 1, 'title' => 'Kedatangan & GWK Cultural Park',          'description' => 'Tiba di Ngurah Rai, check-in hotel, dan kunjungi Garuda Wisnu Kencana.', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['trip_location_id' => $baliLoc->id, 'day' => 2, 'title' => 'Uluwatu Temple & Kecak Dance',            'description' => 'Sunrise di sawah Tegalalang, lanjut ke Pura Uluwatu dan pertunjukan Kecak.', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['trip_location_id' => $baliLoc->id, 'day' => 3, 'title' => 'Ubud Monkey Forest & Rice Terraces',     'description' => 'Jelajahi Hutan Monyet, Pasar Ubud, dan Tegalalang Rice Terrace.', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['trip_location_id' => $baliLoc->id, 'day' => 4, 'title' => 'Nusa Penida Full Day Tour',               'description' => 'Island hopping: Kelingking Beach, Angel Billabong, dan Crystal Bay.', 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['trip_location_id' => $baliLoc->id, 'day' => 5, 'title' => 'Belanja & Kepulangan',                   'description' => 'Pagi bebas, belanja oleh-oleh di Seminyak, transfer bandara.', 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);
        TripAccommodation::insert([
            ['trip_location_id' => $baliLoc->id, 'name' => 'Ayana Resort Bali',    'type' => 'Bintang 5', 'notes' => 'Cliff-top resort dengan infinity pool spektakuler', 'created_at' => now(), 'updated_at' => now()],
            ['trip_location_id' => $baliLoc->id, 'name' => 'Maya Ubud Resort',     'type' => 'Bintang 5', 'notes' => 'Dikelilingi hutan tropis dan sungai Petanu',         'created_at' => now(), 'updated_at' => now()],
        ]);
        $bali->schedules()->createMany([
            ['departure_date' => now()->addDays(10), 'return_date' => now()->addDays(15), 'quota' => 20],
            ['departure_date' => now()->addDays(30), 'return_date' => now()->addDays(35), 'quota' => 20],
        ]);

        // ── Singapore Urban Life (Internasional) ────────────────────────────
        $sg = Trip::create([
            'title'               => 'Singapore Urban Life',
            'slug'                => 'singapore-urban',
            'description'         => 'Rasakan gemerlap kota masa depan: Marina Bay, Gardens by the Bay, Universal Studios, dan kuliner halal khas Asia.',
            'is_domestic'         => false,
            'destination_country' => 'Singapore',
            'price'               => 6800000,
            'duration_days'       => 4,
            'image'               => 'https://images.unsplash.com/photo-1525625293386-3f8f99389edd?q=80&w=1952&auto=format&fit=crop',
        ]);

        $sgLoc = TripLocation::create(['trip_id' => $sg->id, 'country' => 'Singapore', 'city' => 'Singapore City', 'flag_emoji' => '🇸🇬', 'sort_order' => 1]);
        TripItinerary::insert([
            ['trip_location_id' => $sgLoc->id, 'day' => 1, 'title' => 'Kedatangan & Jewel Changi',                'description' => 'Tiba di Jewel Changi Airport, nikmati Rain Vortex, check-in hotel.', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['trip_location_id' => $sgLoc->id, 'day' => 2, 'title' => 'Marina Bay Sands & Gardens by the Bay',   'description' => 'Foto ikonik di MBS, kunjungi Flower Dome dan Cloud Forest GBTB.', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['trip_location_id' => $sgLoc->id, 'day' => 3, 'title' => 'Universal Studios Singapore',             'description' => 'Full day di USS Sentosa Island, makan malam seafood di Clarke Quay.', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['trip_location_id' => $sgLoc->id, 'day' => 4, 'title' => 'Orchard Road & Kepulangan',               'description' => 'Shopping di Orchard Road, oleh-oleh, transfer ke bandara.', 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
        ]);
        TripAccommodation::insert([
            ['trip_location_id' => $sgLoc->id, 'name' => 'Marina Bay Sands',       'type' => 'Bintang 5', 'notes' => 'Iconic infinity pool di lantai 57 dengan view kota terbaik', 'created_at' => now(), 'updated_at' => now()],
            ['trip_location_id' => $sgLoc->id, 'name' => 'Village Hotel Sentosa',  'type' => 'Bintang 4', 'notes' => 'Dekat Universal Studios dan pantai Sentosa',               'created_at' => now(), 'updated_at' => now()],
        ]);
        $sg->schedules()->createMany([
            ['departure_date' => now()->addDays(14), 'return_date' => now()->addDays(18), 'quota' => 15],
            ['departure_date' => now()->addDays(40), 'return_date' => now()->addDays(44), 'quota' => 15],
        ]);

        // ── Thailand Escape (Internasional) ─────────────────────────────────
        $thai = Trip::create([
            'title'               => 'Thailand Escape',
            'slug'                => 'thailand-escape',
            'description'         => 'Bangkok yang ramai bertemu pantai Phuket yang memukau. Perjalanan 5 hari menjelajahi street food, Grand Palace, hingga Phi Phi Island.',
            'is_domestic'         => false,
            'destination_country' => 'Thailand',
            'price'               => 5200000,
            'duration_days'       => 5,
            'image'               => 'https://images.unsplash.com/photo-1552465011-b4e21bf6e79a?q=80&w=2039&auto=format&fit=crop',
        ]);

        $bkk = TripLocation::create(['trip_id' => $thai->id, 'country' => 'Thailand', 'city' => 'Bangkok', 'flag_emoji' => '🇹🇭', 'sort_order' => 1]);
        TripItinerary::insert([
            ['trip_location_id' => $bkk->id, 'day' => 1, 'title' => 'Kedatangan & Street Food Tour Bangkok',     'description' => 'Tiba di Suvarnabhumi, check-in hotel, malam jelajah Yaowarat (Chinatown) dan street food.', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['trip_location_id' => $bkk->id, 'day' => 2, 'title' => 'Grand Palace & Wat Pho',                   'description' => 'Kunjungi Istana Raja (Grand Palace), Wat Phra Kaew, dan Reclining Buddha di Wat Pho.', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);
        TripAccommodation::insert([
            ['trip_location_id' => $bkk->id, 'name' => 'Lebua at State Tower',     'type' => 'Bintang 5', 'notes' => 'Sky Bar tersohor di kota Bangkok, view sungai Chao Phraya', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $pkt = TripLocation::create(['trip_id' => $thai->id, 'country' => 'Thailand', 'city' => 'Phuket & Phi Phi', 'flag_emoji' => '🏝️', 'sort_order' => 2]);
        TripItinerary::insert([
            ['trip_location_id' => $pkt->id, 'day' => 3, 'title' => 'Terbang ke Phuket & Patong Beach',         'description' => 'Penerbangan Bangkok–Phuket, check-in resort, sore bersantai di Patong Beach.', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['trip_location_id' => $pkt->id, 'day' => 4, 'title' => 'Phi Phi Island Hopping',                   'description' => 'Speedboat tour: Maya Bay, Viking Cave, Monkey Beach, dan snorkeling di terumbu karang.', 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['trip_location_id' => $pkt->id, 'day' => 5, 'title' => 'Free Morning & Kepulangan',                'description' => 'Pagi bebas, makan siang seafood di Rawai, transfer bandara Phuket.', 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);
        TripAccommodation::insert([
            ['trip_location_id' => $pkt->id, 'name' => 'Amari Phuket',             'type' => 'Bintang 5', 'notes' => 'Beachfront resort langsung di Patong, kolam renang sinusoidal', 'created_at' => now(), 'updated_at' => now()],
        ]);
        $thai->schedules()->createMany([
            ['departure_date' => now()->addDays(20), 'return_date' => now()->addDays(25), 'quota' => 10],
            ['departure_date' => now()->addDays(50), 'return_date' => now()->addDays(55), 'quota' => 10],
        ]);

        // ── 3 Negara Asia (Malaysia + Singapore + Thailand) ─────────────────
        $triple = Trip::create([
            'title'               => 'Paket 3 Negara Asia',
            'slug'                => '3-negara-asia',
            'description'         => 'Satu perjalanan, tiga budaya! Dari KL yang kosmopolitan, lanjut ke Singapore yang futuristik, hingga Bangkok yang eksotis. 8 hari perjalanan impian.',
            'is_domestic'         => false,
            'destination_country' => 'Malaysia, Singapore, Thailand',
            'price'               => 12500000,
            'duration_days'       => 8,
            'image'               => 'https://images.unsplash.com/photo-1508009603885-50cf7c579365?q=80&w=1950&auto=format&fit=crop',
        ]);

        $kl = TripLocation::create(['trip_id' => $triple->id, 'country' => 'Malaysia', 'city' => 'Kuala Lumpur', 'flag_emoji' => '🇲🇾', 'sort_order' => 1]);
        TripItinerary::insert([
            ['trip_location_id' => $kl->id, 'day' => 1, 'title' => 'Kedatangan KL & Petronas Twin Towers',      'description' => 'Tiba di KLIA2, check-in hotel, malam foto Petronas dari KLCC Park.', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['trip_location_id' => $kl->id, 'day' => 2, 'title' => 'Batu Caves & Pasar Chow Kit',              'description' => 'Naiki 272 anak tangga ke Batu Caves, sore jelajahi kuliner halal di Chow Kit.', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);
        TripAccommodation::insert([
            ['trip_location_id' => $kl->id, 'name' => 'Mandarin Oriental KL',      'type' => 'Bintang 5', 'notes' => 'Di jantung KLCC, view Petronas yang menakjubkan', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $sg2 = TripLocation::create(['trip_id' => $triple->id, 'country' => 'Singapore', 'city' => 'Singapore City', 'flag_emoji' => '🇸🇬', 'sort_order' => 2]);
        TripItinerary::insert([
            ['trip_location_id' => $sg2->id, 'day' => 3, 'title' => 'Naik Bus ke Singapore & Marina Bay',       'description' => 'Perjalanan darat KL–SG, check-in hotel, malam kunjungi Merlion Park.', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['trip_location_id' => $sg2->id, 'day' => 4, 'title' => 'Universal Studios & Sentosa Island',       'description' => 'Full day di Sentosa: USS, S.E.A. Aquarium, dan Siloso Beach.', 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['trip_location_id' => $sg2->id, 'day' => 5, 'title' => 'Gardens by the Bay & Little India',        'description' => 'Pagi di GBTB, siang jelajahi Little India dan Arab Street.', 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);
        TripAccommodation::insert([
            ['trip_location_id' => $sg2->id, 'name' => 'Hotel Vagabond Singapore',  'type' => 'Bintang 4', 'notes' => 'Boutique hotel desain unik di Little India, lokasi strategis', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $bkk2 = TripLocation::create(['trip_id' => $triple->id, 'country' => 'Thailand', 'city' => 'Bangkok', 'flag_emoji' => '🇹🇭', 'sort_order' => 3]);
        TripItinerary::insert([
            ['trip_location_id' => $bkk2->id, 'day' => 6, 'title' => 'Terbang ke Bangkok & Chatuchak Market',   'description' => 'Budget flight SG–BKK, jelajahi pasar terbesar Chatuchak.', 'sort_order' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['trip_location_id' => $bkk2->id, 'day' => 7, 'title' => 'Grand Palace & Floating Market',          'description' => 'Kunjungi Grand Palace pagi hari, sore Damnoen Saduak Floating Market.', 'sort_order' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['trip_location_id' => $bkk2->id, 'day' => 8, 'title' => 'Terminal 21 & Kepulangan',                'description' => 'Belanja di Terminal 21, makan malam terakhir, transfer bandara Suvarnabhumi.', 'sort_order' => 8, 'created_at' => now(), 'updated_at' => now()],
        ]);
        TripAccommodation::insert([
            ['trip_location_id' => $bkk2->id, 'name' => 'Centara Grand Bangkok',   'type' => 'Bintang 5', 'notes' => 'Connected ke CentralWorld Mall, pusat kota Bangkok', 'created_at' => now(), 'updated_at' => now()],
        ]);
        $triple->schedules()->createMany([
            ['departure_date' => now()->addDays(25), 'return_date' => now()->addDays(33), 'quota' => 12],
            ['departure_date' => now()->addDays(60), 'return_date' => now()->addDays(68), 'quota' => 12],
        ]);
        // ── Buat 2 Dummy Pemesanan / Reservasi (Booking) ─────────────────────────
        $admin = \App\Models\User::where('email', 'admin@admin.com')->first();
        if ($bali->schedules()->exists()) {
            $baliSched = $bali->schedules()->first();
            \App\Models\Booking::create([
                'booking_code' => 'TRV-BALI01',
                'schedule_id'    => $baliSched->id,
                'customer_name'  => 'Agus Hariyanto (DUMMY)',
                'customer_email' => 'agus@example.com',
                'customer_phone' => '081234560001',
                'pax'            => 2,
                'total_amount'   => 2 * $bali->price,
                'status'         => 'paid',
                'xendit_invoice_id' => 'invoice-agus-dummy-123',
                'xendit_invoice_url' => 'https://checkout-staging.xendit.co/web/dummy-lunas'
            ]);
            $baliSched->decrement('quota', 2);
        }

        if ($triple->schedules()->exists()) {
            \App\Models\Booking::create([
                'booking_code' => 'TRV-TRIP01',
                'schedule_id'    => $triple->schedules()->first()->id,
                'customer_name'  => 'Sinta Devyani (DUMMY)',
                'customer_email' => 'sinta@example.com',
                'customer_phone' => '081298765432',
                'pax'            => 1,
                'total_amount'   => 1 * $triple->price,
                'status'         => 'pending',
                'xendit_invoice_id' => 'invoice-sinta-dummy-456',
                'xendit_invoice_url' => 'https://checkout-staging.xendit.co/web/dummy-pending'
            ]);
        }
    }
}
