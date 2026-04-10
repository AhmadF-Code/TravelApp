<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RefundBookingResource\Pages;
use App\Models\Booking;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;

class RefundBookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Audit & Financial Refund';
    protected static ?string $navigationGroup = 'Operational';
    protected static ?int $navigationSort = 2;
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('follow_up_status', 'refund_processed')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereNotNull('refund_amount')
            ->whereIn('follow_up_status', ['refund_processed', 'refund_completed'])
            ->with(['refundProcessor']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRefundBookingsStable::route('/'),
            'proses' => Pages\ProsesRefund::route('/{record}/proses'),
            'view' => Pages\ViewRefundBooking::route('/{record}'),
        ];
    }
}
