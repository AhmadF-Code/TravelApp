<?php

namespace App\Filament\Resources\RefundBookingResource\Pages;

use App\Filament\Resources\RefundBookingResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;

class ProsesRefund extends EditRecord
{
    protected static string $resource = RefundBookingResource::class;

    protected static ?string $title = 'Penyelesaian Refund';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Validasi Refund')
                    ->description('Pastikan dana telah dikirimkan ke pelanggan sebelum mencatat status Complete.')
                    ->schema([
                        Forms\Components\Grid::make(3)->schema([
                            Forms\Components\TextInput::make('booking_code')
                                ->label('Kode Booking')
                                ->disabled(),
                            Forms\Components\TextInput::make('customer_name')
                                ->label('Nama Pelanggan')
                                ->disabled(),
                            Forms\Components\TextInput::make('refund_amount')
                                ->label('Jumlah Refund')
                                ->prefix('Rp')
                                ->disabled(),
                        ]),
                        
                        Forms\Components\DatePicker::make('refund_completed_at')
                            ->label('Tanggal Transfer/Refund Dilakukan')
                            ->default(now())
                            ->required(),

                        Forms\Components\FileUpload::make('refund_proof_image')
                            ->label('Bukti Transfer / Refund (Screenshot)')
                            ->image()
                            ->directory('refund-proofs')
                            ->required()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->label('Selesaikan & Simpan Bukti')
                ->color('success'),
            $this->getCancelFormAction()
                ->label('Batal'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['follow_up_status'] = 'refund_completed';
        $data['refund_processed_by_id'] = auth()->id();
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Refund Selesai')
            ->body('Status refund telah diubah menjadi Complete dan bukti telah disimpan.');
    }
}
