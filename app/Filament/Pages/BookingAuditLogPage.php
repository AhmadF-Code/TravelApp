<?php

namespace App\Filament\Pages;

use App\Models\BookingAuditLog;
use Filament\Pages\Page;
use Illuminate\Pagination\LengthAwarePaginator;

class BookingAuditLogPage extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'System';
    protected static ?string $navigationLabel = 'Audit Log Booking';
    protected static ?string $title           = 'Audit Log Booking';
    protected static ?int    $navigationSort  = 99;
    protected static string  $view            = 'filament.pages.booking-audit-log';

    public string $search     = '';
    public string $actionFilter = 'all';

    public function mount(): void
    {
        $this->search       = request()->query('search', '');
        $this->actionFilter = request()->query('action', 'all');
    }

    public function getRecordsProperty(): LengthAwarePaginator
    {
        $query = BookingAuditLog::query()->latest('id');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('booking_code', 'like', "%{$this->search}%")
                  ->orWhere('triggered_by', 'like', "%{$this->search}%")
                  ->orWhere('notes', 'like', "%{$this->search}%");
            });
        }

        if ($this->actionFilter !== 'all') {
            $query->where('action', $this->actionFilter);
        }

        return $query->paginate(30)->withQueryString();
    }

    /**
     * Summary stats for the header KPI cards.
     */
    public function getStatsProperty(): array
    {
        return [
            'total'        => BookingAuditLog::count(),
            'auto_expired' => BookingAuditLog::where('action', 'auto_expired')->count(),
            'today'        => BookingAuditLog::whereDate('created_at', today())->count(),
            'today_expired'=> BookingAuditLog::where('action', 'auto_expired')->whereDate('created_at', today())->count(),
        ];
    }
}
