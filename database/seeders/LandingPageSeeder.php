<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\LandingPage;

class LandingPageSeeder extends Seeder
{
    public function run(): void
    {
        // Delete all previously created records to ensure they match the new schema exactly
        LandingPage::truncate();

        // ── ACTIVE VERSION (live) ────────────────────────────────────────────
        LandingPage::create([
            'title'      => 'Homepage v1 — Initial Launch',
            'is_active'  => true,
            'status'     => 'active',
            'version'    => 1,
            'change_summary' => 'Initial launch version',
            'published_by'   => 'system',
            'published_at'   => now(),

            'meta_title'       => 'AVRA Tour — Premium Tour & Travel Indonesia',
            'meta_description' => 'Agen perjalanan terbaik untuk wisata domestik dan internasional: Bali, Singapore, Malaysia, Thailand dan lebih banyak lagi.',
            'meta_keywords'    => 'travel, tour, liburan, paket wisata, domestik, internasional, agen perjalanan',
            'gtm_id'           => 'XXXXXXX',

            'hero_title'            => 'Perjalanan Impian,',
            'hero_subtitle'         => 'Kami yang Urus',
            'hero_text'             => 'Eksplorasi destinasi <span class="text-[#FAE251] font-black">internasional</span> & <span class="text-[#FAE251] font-black">domestik</span> tanpa ribet. Tiket, hotel, hingga itinerary lengkap—semua kami siapkan untuk liburan impian Anda.',
            'hero_background_image' => 'https://images.unsplash.com/photo-1508009603885-50cf7c579365?q=80&w=2350&auto=format&fit=crop',

            'featured_trip_title'    => 'Paket Trip Unggulan',
            'featured_trip_subtitle' => 'Destinasi Pilihan',

            'about_title'    => 'Mitra Perjalanan<br>Yang Bisa Dipercaya',
            'about_subtitle' => 'Tentang AVRA Tour',
            'about_text'     => 'Bebas overthinking, Bestie. Ribuan traveler sudah membuktikannya. Fokus kami cuma satu: bikin momen liburanmu epic & gak terlupakan.',
            'about_image'    => 'https://images.unsplash.com/photo-1522199710521-72d69614c702?q=80&w=2072&auto=format&fit=crop',

            'youtube_url'    => 'https://www.youtube.com/embed/M0AqlfISV0A?rel=0&modestbranding=1',
            'gallery_images' => [
                'https://images.unsplash.com/photo-1528127269322-539801943592?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1507608616759-54f48f0af0ee?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1533587851505-d119e13bf0b7?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1526778548025-fa2f459cd5c1?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1501785888041-af3ef285b470?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?auto=format&fit=crop&q=80&w=800',
            ],

            'testimonial_title' => 'Yang Mereka Katakan',
            'testimonials' => [
                ['name' => 'Sarah Jenkins', 'quote' => 'Trip ke Thailand kami berjalan sempurna. Pemandu luar biasa dan setiap detail sudah diurus.', 'stars' => 5],
                ['name' => 'Budi Santoso', 'quote' => 'Bali sungguh magis! Berkat AVRA Tour, kami dapat harga terbaik tanpa khawatir apapun.', 'stars' => 5],
                ['name' => 'Emily Wong', 'quote' => 'Sangat rekomendasikan paket Singapore-nya. Hotel yang dipilihkan tepat di sebelah Marina Bay!', 'stars' => 5],
            ],

            'cta_title'    => 'Siap Memulai Petualangan?',
            'cta_subtitle' => 'Destinasi impian Anda hanya beberapa klik lagi.',

            'footer_text'    => 'Menghubungkan Anda ke destinasi paling indah di dunia sejak 2011.',
            'footer_email'   => 'info@avratour.com',
            'footer_phone'   => '+62 812 3456 7890',
            'footer_address' => 'Jakarta, Indonesia',
        ]);

        $this->command->info('Landing page seeded: 1 ACTIVE version with full dynamic content.');
    }
}
