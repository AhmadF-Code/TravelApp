<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function calendar()
    {
        $schedules = Schedule::with(['trip', 'bookings'])->get()->map(fn($s) => [
            'id' => $s->id,
            'title' => ($s->trip->title ?? 'N/A'),
            'start' => $s->departure_date ? $s->departure_date->format('Y-m-d') : null,
            'end' => $s->return_date ? $s->return_date->addDay()->format('Y-m-d') : null,
            'backgroundColor' => $s->status === 'cancelled' ? '#F43F5E' : (now()->startOfDay()->greaterThan($s->departure_date) ? '#10B981' : '#3B82F6'),
            'borderColor' => 'transparent',
            'extendedProps' => [
                'id' => $s->id,
                'quota' => $s->quota,
                'pax_paid' => $s->bookings->where('status', 'paid')->sum('pax'),
                'pax_pending' => $s->bookings->where('status', 'pending')->sum('pax'),
                'pax_cancelled' => $s->bookings->where('status', 'cancelled')->sum('pax'),
                'remaining' => (int)$s->quota - (int)$s->bookings->where('status', 'paid')->sum('pax'),
                'status' => $s->status,
                'trip_name' => $s->trip->title ?? 'N/A',
                'dates' => ($s->departure_date ? $s->departure_date->format('d M') : 'N/A') . ' - ' . ($s->return_date ? $s->return_date->format('d M Y') : 'N/A'),
            ]
        ]);

        return view('admin.calendar', compact('schedules'));
    }
}
