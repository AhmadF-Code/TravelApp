<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Http\Controllers\FrontendController;
use App\Http\Controllers\ReportController;

// ── Admin Report Downloads ─────────────────────────────────────────────────
Route::get('/admin/report/download/{type}', [ReportController::class, 'download'])
    ->middleware(['web', 'auth'])
    ->name('admin.report.download');

Route::get('/', [FrontendController::class, 'index'])->name('home');
Route::get('/destinations', [FrontendController::class, 'destinations'])->name('destinations');
Route::get('/trip/{slug}', [FrontendController::class, 'show'])->name('trip.show');
Route::post('/trip/{slug}/book', [FrontendController::class, 'book'])->name('trip.book');
Route::get('/trip/{slug}/detail', [FrontendController::class, 'apiDetail'])->name('trip.api.detail');
Route::get('/api/check-promo/{code}', [FrontendController::class, 'checkPromo'])->name('api.check.promo');
Route::post('/api/analytics/event', function(Illuminate\Http\Request $request) {
    \App\Services\AnalyticsService::logEvent(
        $request->event_type,
        $request->event_name,
        $request->metadata ?? []
    );
    return response()->json(['success' => true]);
})->middleware(['web']);

Route::get('/schedules', [FrontendController::class, 'schedules'])->name('schedules.index');

Route::get('/admin/standalone-calendar', [App\Http\Controllers\Admin\AdminDashboardController::class, 'calendar'])
    ->middleware(['web', 'auth'])
    ->name('admin.calendar_standalone');

// Admin Helper Routes (for Calendar)
Route::post('/admin/schedules/cancel/{id}', function ($id) {
    $schedule = \App\Models\Schedule::findOrFail($id);
    $schedule->update(['status' => 'cancelled']);
    $schedule->bookings()->where('status', 'paid')->update([
        'follow_up_status' => 'needs_follow_up',
        'follow_up_note' => 'Jadwal dibatalkan sistem melalui Kalender Admin.'
    ]);
    return response()->json(['success' => true]);
})->middleware(['web', 'auth']);

Route::get('/cek-pesanan', [FrontendController::class, 'cekPesanan'])->name('booking.cek');
Route::post('/cek-pesanan', [FrontendController::class, 'cekPesanan'])->name('booking.search');
Route::get('/cek-pesanan/{code}', [FrontendController::class, 'showPesanan'])->name('booking.show');
Route::get('/checkout/{code}', [FrontendController::class, 'checkout'])->name('booking.checkout');
Route::post('/cek-pesanan/{code}/pay', [FrontendController::class, 'regeneratePayment'])->name('booking.repay');

Route::post('/midtrans/webhook', [FrontendController::class, 'midtransWebhook']);

// ── Landing Page Staging PREVIEW route ──────────────────────────────────────
// Loads DRAFT version — protected so only admins can see it
Route::get('/preview-landing', [FrontendController::class, 'previewLanding'])
    ->middleware(['web', 'auth'])
    ->name('landing.preview');

// Batch Cancel Schedule and Move to Follow-up
Route::get('/admin/schedules/cancel/{id}', function ($id) {
    $schedule = \App\Models\Schedule::findOrFail($id);
    $schedule->update(['status' => 'cancelled']);
    
    // Flag all paid bookings as needing follow up
    $schedule->bookings()->where('status', 'paid')->update([
        'status' => 'on_followup',
        'follow_up_status' => 'needs_follow_up',
        'follow_up_note' => 'Jadwal dibatalkan sistem oleh admin via Tombol Batal.'
    ]);
    
    return redirect()->to(\App\Filament\Resources\FollowUpBookingResource::getUrl('index'));
})->middleware(['web', 'auth'])->name('admin.schedules.cancel_manual');
