<?php

namespace App\Filament\Resources\FollowUpBookingResource\Pages;

use App\Filament\Resources\FollowUpBookingResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;

class CancelRefundBooking extends EditRecord
{
    protected static string $resource = FollowUpBookingResource::class;

    protected static ?string $title = 'Batalkan & Refund Booking';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pembatalan')
                    ->description('Tindakan ini akan membatalkan pesanan dan mencatat pengembalian dana.')
                    ->schema([
                        Forms\Components\TextInput::make('booking_code')
                            ->disabled()
                            ->label('Kode Booking'),
                        Forms\Components\TextInput::make('customer_name')
                            ->disabled()
                            ->label('Nama Pelanggan'),
                        Forms\Components\TextInput::make('total_amount')
                            ->label('Total Terbayar')
                            ->prefix('Rp')
                            ->disabled(),
                        Forms\Components\TextInput::make('refund_amount')
                            ->label('Jumlah Refund')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->placeholder('Masukkan total dana dikembalikan...'),
                        Forms\Components\Textarea::make('follow_up_note')
                            ->label('Alasan Pembatalan & Refund')
                            ->required()
                            ->placeholder('Contoh: Pembatalan sepihak karena jadwal tidak cocok.'),
                    ])->columns(2),
            ]);
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->label('Batalkan & Refund')
                ->color('danger'),
            $this->getCancelFormAction()
                ->label('Kembali'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['status'] = 'cancelled';
        $data['follow_up_status'] = 'refund_processed';
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->warning()
            ->title('Pesanan Dibatalkan')
            ->body('Booking telah dipidahkan ke status cancelled dan refund telah dicatat.');
    }
}
