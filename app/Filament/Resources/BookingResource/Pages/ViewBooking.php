<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\DB;

class ViewBooking extends ViewRecord
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('refund_all')
                ->label('Refund Seluruh Grup')
                ->icon('heroicon-o-banknotes')
                ->color('danger')
                ->form([
                    \Filament\Forms\Components\TextInput::make('refund_amount')
                        ->label('Total Nominal Refund (IDR)')
                        ->numeric()
                        ->default(fn ($record) => $record->total_amount)
                        ->required(),
                    \Filament\Forms\Components\Textarea::make('reason')
                        ->label('Alasan Pembatalan Grup')
                        ->required(),
                ])
                ->action(function ($record, array $data) {
                    DB::transaction(function () use ($record, $data) {
                        $refundAmount = (float)$data['refund_amount'];

                        // 1. Cancel Entire Booking
                        $record->update([
                            'status' => 'cancelled',
                            'total_amount' => 0, // Fully refunded
                            'refund_amount' => (float)$record->refund_amount + $refundAmount,
                            'follow_up_status' => 'refund_processed',
                            'follow_up_note' => ($record->follow_up_note ? $record->follow_up_note . "\n" : "") . 
                                              "REFUND GRUP TOTAL — " . $data['reason'] . " (Rp " . number_format($refundAmount) . ")",
                        ]);

                        // 2. Cancel ALL travelers
                        $record->travelers()->update(['status' => 'cancelled']);
                    });

                    \Filament\Notifications\Notification::make()
                        ->title('Seluruh Grup Berhasil Direfund')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->visible(fn ($record) => $record->status !== 'cancelled'),
        ];
    }
}
