<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\Schedule;
use App\Models\Booking;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingPendingMail;
use App\Mail\BookingPaidMail;

class FrontendController extends Controller
{
    public function index(Request $request)
    {
        $trips       = Trip::with('schedules')->limit(3)->get();
        // Always load ACTIVE (live) version only – never draft
        $landingPage = \App\Models\LandingPage::active() ?? \App\Models\LandingPage::first();
        $galleries   = \App\Models\Gallery::where('is_published', true)->orderBy('sort_order')->get();
        
        $seoSettings = \App\Models\Setting::where('group', 'seo')->get()->pluck('value', 'key');

        return view('welcome', compact('trips', 'landingPage', 'galleries', 'seoSettings'));
    }

    public function previewLanding(Request $request)
    {
        $trips       = Trip::with('schedules')->limit(3)->get();
        // Load DRAFT version for preview; fallback to active if no draft exists
        $landingPage = \App\Models\LandingPage::latestDraft()
                    ?? \App\Models\LandingPage::active()
                    ?? \App\Models\LandingPage::first();
        $galleries   = \App\Models\Gallery::where('is_published', true)->orderBy('sort_order')->get();
        $seoSettings = \App\Models\Setting::where('group', 'seo')->get()->pluck('value', 'key');

        // Inject a preview banner flag
        return view('welcome', compact('trips', 'landingPage', 'galleries', 'seoSettings'))
                ->with('isPreviewMode', true);
    }

    public function destinations()
    {
        $trips = Trip::with('schedules')->get();
        return view('destinations', compact('trips'));
    }

    public function schedules()
    {
        $schedules = Schedule::with('trip')
            ->where('departure_date', '>=', now())
            ->orderBy('departure_date', 'asc')
            ->get();
        return view('schedules', compact('schedules'));
    }

    public function show($slug)
    {
        $trip = Trip::where('slug', $slug)
            ->with(['schedules', 'locations.itineraries', 'locations.accommodations'])
            ->firstOrFail();
        
        $branches = \App\Models\Branch::where('is_active', true)->get();
        
        return view('trip.show', compact('trip', 'branches'));
    }

    /**
     * AJAX: return full trip detail JSON for the modal
     */
    public function apiDetail($slug)
    {
        $trip = Trip::where('slug', $slug)
            ->with(['locations' => function ($q) {
                $q->orderBy('sort_order')
                  ->with([
                      'itineraries' => fn ($q) => $q->orderBy('sort_order')->orderBy('day'),
                      'accommodations',
                  ]);
            }])
            ->firstOrFail();

        return response()->json([
            'id'                 => $trip->id,
            'title'              => $trip->title,
            'slug'               => $trip->slug,
            'description'        => $trip->description,
            'price'              => $trip->price,
            'duration_days'      => $trip->duration_days,
            'destination_country'=> $trip->destination_country,
            'is_domestic'        => $trip->is_domestic,
            'image'              => $trip->image_url,
            'locations'          => $trip->locations->map(function ($loc) {
                return [
                    'country'        => $loc->country,
                    'city'           => $loc->city,
                    'flag_emoji'     => $loc->flag_emoji,
                    'itineraries'    => $loc->itineraries->map(fn ($i) => [
                        'day'         => $i->day,
                        'title'       => $i->title,
                        'description' => $i->description,
                    ]),
                    'accommodations' => $loc->accommodations->map(fn ($a) => [
                        'name'  => $a->name,
                        'type'  => $a->type,
                        'notes' => $a->notes,
                    ]),
                ];
            }),
        ]);
    }

    public function book(Request $request, $slug)
    {
        $trip = Trip::where('slug', $slug)->firstOrFail();
        
        $rules = [
            'schedule_id'    => 'required|exists:schedules,id',
            'customer_name'  => 'required|string|min:2|max:100',
            'customer_email' => 'required|email:rfc,dns',
            'customer_phone' => ['required', 'string', 'regex:/^(\+62|62|0)[0-9]{8,13}$/'],
            'pax'            => 'required|integer|min:1|max:100',
            'branch_id'      => 'required|exists:branches,id',
            'promo_code_input'=> 'nullable|string',
        ];

        if (!$trip->is_domestic) {
            $rules['primary_passport'] = 'required|string|min:6|max:20';
        }

        $request->validate($rules, [
            'customer_phone.regex' => 'Nomor HP tidak valid. Gunakan format: 08xx, 628xx, atau +628xx.',
            'primary_passport.required' => 'Nomor Passport wajib diisi untuk trip internasional.',
        ]);

        $schedule = Schedule::where('id', $request->schedule_id)
                            ->where('trip_id', $trip->id)
                            ->firstOrFail();

        $fallbackMessage = null;
        if ($schedule->quota < $request->pax) {
            $nextSchedule = Schedule::where('trip_id', $trip->id)
                ->where('departure_date', '>=', now())
                ->where('quota', '>=', $request->pax)
                ->orderBy('departure_date', 'asc')
                ->first();

            if ($nextSchedule) {
                $schedule = $nextSchedule;
                $fallbackMessage = 'Jadwal pilihan Anda sudah penuh. Anda otomatis dipindahkan ke keberangkatan berikutnya: ' . \Carbon\Carbon::parse($schedule->departure_date)->format('d M Y');
                \Illuminate\Support\Facades\Session::flash('warning', $fallbackMessage);
            } else {
                return back()->withErrors(['pax' => 'Maaf, semua jadwal untuk trip ini sudah penuh atau sisa kuota tidak mencukupi.'])->withInput();
            }
        }

        $total_amount = $trip->price * $request->pax;

        // NEW: Promo Code Logic
        $discount_amount = 0;
        $applied_promo_code = null;
        if ($request->promo_code_input) {
            $promo = \App\Models\PromoCode::where('code', $request->promo_code_input)
                ->where('is_active', true)
                ->where(function($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })
                ->first();
            
            if ($promo) {
                $discount_amount = $promo->discount_amount;
                $applied_promo_code = $promo->code;
                $total_amount = max(0, $total_amount - $discount_amount);
            }
        }

        $booking = Booking::create([
            'schedule_id'         => $schedule->id,
            'customer_name'       => $request->customer_name,
            'customer_email'      => $request->customer_email,
            'customer_phone'      => $request->customer_phone,
            'branch_id'           => $request->branch_id,
            'promo_code'          => $applied_promo_code,
            'discount_amount'     => $discount_amount,
            'pax'                 => $request->pax,
            'primary_passport'    => $request->primary_passport,
            'total_amount'        => $total_amount,
            'status'              => 'pending',
        ]);

        \App\Services\AnalyticsService::logEvent('booking_created', $booking->booking_code, [
            'trip_id' => $trip->id,
            'amount'  => $total_amount
        ]);

        // 1. Create Primary Traveler
        $booking->travelers()->create([
            'name' => $request->customer_name,
            'email' => $request->customer_email,
            'phone' => $request->customer_phone,
            'passport_number' => $request->primary_passport,
            'is_primary' => true,
            'status' => 'pending',
        ]);

        // 2. Create Additional Travelers
        if ($request->pax > 1) {
            for ($i = 1; $i < $request->pax; $i++) {
                $booking->travelers()->create([
                    'name' => $request->input("additional_names.$i"),
                    'email' => $request->input("additional_emails.$i"),
                    'phone' => $request->input("additional_phones.$i"),
                    'passport_number' => $request->input("additional_passports.$i"),
                    'is_primary' => false,
                    'status' => 'pending',
                ]);
            }
        }

        // Quota is now managed dynamically via count, no manual decrement needed

        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = config('services.midtrans.is_sanitized');
        Config::$is3ds = config('services.midtrans.is_3ds');

        $orderId = 'booking-' . $booking->id . '-' . time();
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $total_amount,
            ],
            // ... (customer details)
            'customer_details' => [
                'first_name' => $booking->customer_name,
                'email' => $booking->customer_email,
                'phone' => $booking->customer_phone,
            ],
            'enabled_payments' => ['credit_card', 'mandiri_clickpay', 'cimb_clicks', 'bca_klikbca', 'bca_klikpay', 'bri_epay', 'echannel', 'permata_va', 'bca_va', 'bni_va', 'bri_va', 'other_va', 'gopay', 'indomaret', 'alfamart', 'shopeepay'],
        ];

        try {
            $snapResponse = Snap::createTransaction($params);
            $booking->update([
                'midtrans_snap_token'  => $snapResponse->token,
                'midtrans_redirect_url' => $snapResponse->redirect_url,
                'midtrans_order_id'    => $orderId,
            ]);
        } catch (\Exception $e) {
            Log::info('Midtrans transaction creation failed - saving as PENDING', ['booking_id' => $booking->id, 'error' => $e->getMessage()]);
        }

        $redirect_to = route('booking.checkout', $booking->booking_code);
        request()->session()->put('auth_booking_' . $booking->booking_code, true);

        try {
            Mail::to($booking->customer_email)->send(new BookingPendingMail($booking));
        } catch (\Exception $e) {
            Log::error('Failed to send pending email: ' . $e->getMessage());
        }

        $redirect = redirect($redirect_to)->with('message', 'Pemesanan disimpan. Silakan lakukan pembayaran dalam 1 jam.');
        
        if ($fallbackMessage) {
            $redirect->with('warning', $fallbackMessage);
        }

        return $redirect;
    }

    public function midtransWebhook(Request $request)
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');

        try {
            $notif = new Notification();
        } catch (\Exception $e) {
            Log::error('Midtrans Webhook Error: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }

        $transaction = $notif->transaction_status;
        $type = $notif->payment_type;
        $orderId = $notif->order_id;
        $fraud = $notif->fraud_status;

        // order_id format: booking-{id}-{timestamp}
        $parts = explode('-', $orderId);
        if (count($parts) >= 2 && $parts[0] === 'booking') {
            $bookingId = (int) $parts[1];
            $booking = Booking::find($bookingId);

            if ($booking) {
                if ($transaction == 'capture') {
                    if ($type == 'credit_card') {
                        if ($fraud == 'challenge') {
                            $booking->update(['status' => 'pending']);
                        } else {
                            $this->markAsPaid($booking);
                        }
                    }
                } else if ($transaction == 'settlement') {
                    $this->markAsPaid($booking);
                } else if ($transaction == 'pending') {
                    $booking->update(['status' => 'pending']);
                } else if ($transaction == 'deny' || $transaction == 'expire' || $transaction == 'cancel') {
                    $booking->update(['status' => 'cancelled']);
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }

    private function markAsPaid($booking)
    {
        if ($booking->status !== 'paid') {
            $booking->update(['status' => 'paid']);
            \App\Services\AnalyticsService::logEvent('payment_success', $booking->booking_code, [
                'amount' => $booking->total_amount
            ]);
            Log::info('Booking paid via Midtrans', ['booking_id' => $booking->id]);

            try {
                Mail::to($booking->customer_email)->send(new BookingPaidMail($booking));
            } catch (\Exception $e) {
                Log::error('Failed to send paid email: ' . $e->getMessage());
            }
        }
    }

    public function cekPesanan(Request $request)
    {
        if ($request->isMethod('post')) {
            $code = trim($request->input('code'));
            $email = trim($request->input('email'));
            $travelerCode = trim($request->input('traveler_code'));
            
            if ($code && $email && $travelerCode) {
                $booking = Booking::where('booking_code', $code)
                    ->whereHas('travelers', function($q) use ($email, $travelerCode) {
                        $q->where('email', $email)->where('traveler_code', $travelerCode);
                    })
                    ->first();

                if ($booking) {
                    request()->session()->put('auth_booking_' . $booking->booking_code, true);
                    return redirect()->route('booking.show', $code);
                } else {
                    return back()->with('warning', 'Kombinasi Kode Booking, Email, dan ID Kursi tidak ditemukan.');
                }
            }
        }
        return view('cek-pesanan');
    }

    public function showPesanan($code)
    {
        if (!request()->session()->has('auth_booking_' . $code)) {
            return redirect()->route('booking.cek')->with('warning', 'Sesi pelacakan Anda telah kedaluwarsa atau tidak diizinkan. Silakan login kembali dengan Email Anda.');
        }

        $booking = Booking::with('schedule.trip')->where('booking_code', $code)->firstOrFail();

        // Cek jika sudah expired > 1 jam
        if (strtolower($booking->status) === 'pending' && $booking->created_at->addHour()->isPast()) {
            $booking->update(['status' => 'cancelled']);
            request()->session()->forget('auth_booking_' . $code);
            return redirect()->route('booking.cek')->with('warning', 'Request Anda dibatalkan, silakan order kembali.');
        }

        if (strtolower($booking->status) === 'cancelled' || strtolower($booking->status) === 'canceled') {
             return redirect()->route('booking.cek')->with('warning', 'Request Anda dibatalkan, silakan order kembali.');
        }

        // Shopee-style: Jika masih pending, arahkan ke halaman checkout, BUKAN halaman detail tiket
        if (strtolower($booking->status) === 'pending') {
            return redirect()->route('booking.checkout', $code);
        }

        return view('cek-pesanan', compact('booking'));
    }

    public function checkout($code)
    {
        if (!request()->session()->has('auth_booking_' . $code)) {
            return redirect()->route('booking.cek')->with('warning', 'Sesi Anda telah kedaluwarsa.');
        }

        $booking = Booking::with('schedule.trip')->where('booking_code', $code)->firstOrFail();

        if (strtolower($booking->status) !== 'pending') {
            return redirect()->route('booking.show', $code);
        }

        // Cek jika sudah expired > 1 jam atau status cancel
        if ($booking->created_at->addHour()->isPast() || strtolower($booking->status) === 'cancelled' || strtolower($booking->status) === 'canceled') {
            $booking->update(['status' => 'cancelled']);
            return redirect()->route('booking.cek')->with('warning', 'Request Anda dibatalkan, silakan order kembali.');
        }

        return view('checkout', compact('booking'));
    }

    public function regeneratePayment($code)
    {
        $booking = Booking::with('schedule.trip')->where('booking_code', $code)->firstOrFail();

        if (!request()->session()->has('auth_booking_' . $code)) {
            abort(403);
        }

        if (strtolower($booking->status) !== 'pending') {
            return back()->with('warning', 'Pesanan ini sudah tidak dalam status tertunda.');
        }

        try {
            Config::$serverKey = config('services.midtrans.server_key');
            Config::$isProduction = config('services.midtrans.is_production');
            Config::$isSanitized = config('services.midtrans.is_sanitized');
            Config::$is3ds = config('services.midtrans.is_3ds');

            $orderId = 'booking-' . $booking->id . '-' . time();
            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $booking->total_amount,
                ],
                'customer_details' => [
                    'first_name' => $booking->customer_name,
                    'email' => $booking->customer_email,
                    'phone' => $booking->customer_phone,
                ],
            ];

            $snapResponse = Snap::createTransaction($params);
            $booking->update([
                'midtrans_snap_token'  => $snapResponse->token,
                'midtrans_redirect_url' => $snapResponse->redirect_url,
                'midtrans_order_id'    => $orderId,
            ]);

            return redirect($snapResponse->redirect_url);
        } catch (\Exception $e) {
            Log::error('Regenerate Midtrans Payment Gagal', ['msg' => $e->getMessage()]);
            return back()->with('warning', 'Gagal membangkitkan link pembayaran. Silakan hubungi admin.');
        }
    }

    public function checkPromo($code)
    {
        $promo = \App\Models\PromoCode::where('code', $code)
            ->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->first();

        if ($promo) {
            return response()->json([
                'valid'    => true,
                'discount' => $promo->discount_amount,
                'message'  => 'Promo digunakan: Hemat Rp ' . number_format($promo->discount_amount, 0, ',', '.'),
            ]);
        }

        return response()->json([
            'valid'    => false,
            'discount' => 0,
            'message'  => 'Kode promo tidak valid atau sudah kedaluwarsa.',
        ]);
    }
}
