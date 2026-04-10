<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FollowUpBookingResource\Pages;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FollowUpBookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationGroup = 'Operational';

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Follow-up Bookings';
    
    protected static ?string $pluralLabel = 'Follow-up Bookings';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('follow_up_status', 'needs_follow_up');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('follow_up_status', 'needs_follow_up')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function table(Table $table): Table
    {
        return BookingResource::table($table)
            ->recordTitleAttribute('customer_name')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('follow_up_status', 'needs_follow_up'))
            ->actions([
                // 1. RESCHEDULE ACTION
                Tables\Actions\Action::make('pindah_jadwal')
                    ->label('Pindah Jadwal')
                    ->icon('heroicon-o-arrows-right-left')
                    ->color('info')
                    ->modalHeading('Pindah Keberangkatan')
                    ->modalDescription('Pindahkan booking ke tanggal lain dalam jenis trip yang sama.')
                    ->form([
                        Forms\Components\Select::make('new_schedule_id')
                            ->label('Jadwal Baru')
                            ->options(function ($record) {
                                return \App\Models\Schedule::where('trip_id', $record->schedule->trip_id)
                                    ->where('id', '!=', $record->schedule_id)
                                    ->where('status', 'active')
                                    ->get()
                                    ->mapWithKeys(function ($s) {
                                        $booked = $s->bookings()->whereIn('status', ['paid', 'pending', 'confirmed'])->sum('pax');
                                        $remaining = max(0, $s->quota - $booked);
                                        return [$s->id => \Carbon\Carbon::parse($s->departure_date)->format('d M Y') . " (Sisa: {$remaining} kursi)"];
                                    });
                            })
                            ->required(),
                        Forms\Components\Textarea::make('note')
                            ->label('Catatan')
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $newSchedule = \App\Models\Schedule::findOrFail($data['new_schedule_id']);
                        $booked = $newSchedule->bookings()->whereIn('status', ['paid', 'pending', 'confirmed'])->sum('pax');
                        $remaining = $newSchedule->quota - $booked;

                        if ($remaining < $record->pax) {
                            \Filament\Notifications\Notification::make()
                                ->title('Kuota Gagal')
                                ->body('Sisa kursi pada jadwal baru tidak mencukupi.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $record->update([
                            'schedule_id' => $data['new_schedule_id'],
                            'follow_up_status' => 'resolved_moved',
                            'follow_up_note' => $data['note'],
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Berhasil Pindah Jadwal')
                            ->success()
                            ->send();
                    }),

                // 2. CANCEL & REFUND ACTION
                Tables\Actions\Action::make('cancel_refund')
                    ->label('Batal & Refund')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->modalHeading('Batalkan & Proses Refund')
                    ->modalDescription('Pesanan akan langsung berubah status menjadi Cancelled dan dana refund akan dicatat.')
                    ->form([
                        Forms\Components\TextInput::make('refund_amount')
                            ->label('Jumlah Refund (IDR)')
                            ->numeric()
                            ->default(fn($record) => $record->total_amount)
                            ->required(),
                        Forms\Components\Textarea::make('note')
                            ->label('Alasan Pembatalan/Refund')
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status' => 'cancelled',
                            'follow_up_status' => 'refund_processed',
                            'follow_up_note' => $data['note'],
                            'refund_amount' => $data['refund_amount'],
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Booking Dibatalkan')
                            ->body('Status telah diupdate menjadi CANCELLED dan nominal refund tersimpan.')
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFollowUpBookingsStable::route('/'),
            'reschedule' => Pages\RescheduleBooking::route('/{record}/reschedule'),
            'cancel' => Pages\CancelRefundBooking::route('/{record}/cancel'),
        ];
    }
}
