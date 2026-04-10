<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use App\Filament\Resources\RefundBookingResource;
use App\Models\Booking;
use App\Models\BookingTraveler;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class RefundTraveler extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = BookingResource::class;

    protected static string $view = 'filament.resources.booking-resource.pages.refund-traveler';

    public ?int $record = null;
    public ?int $traveler = null;

    public ?array $data = [];

    public function mount($record, $traveler): void
    {
        $this->record = $record;
        $this->traveler = $traveler;
        $this->form->fill();
    }

    public function getTravelerProperty()
    {
        return BookingTraveler::with('booking')->find($this->traveler);
    }

    public function form(Form $form): Form
    {
        $traveler = $this->getTravelerProperty();
        $unitPrice = $traveler ? ($traveler->booking->total_amount / $traveler->booking->pax) : 0;

        return $form
            ->schema([
                Section::make('Informasi Peserta')
                    ->description('Rincian peserta yang akan direfund')
                    ->schema([
                        Placeholder::make('name')
                            ->label('Nama Peserta')
                            ->content($traveler?->name),
                        Placeholder::make('traveler_code')
                            ->label('ID Kursi')
                            ->content($traveler?->traveler_code),
                        Placeholder::make('booking_code')
                            ->label('Booking Group')
                            ->content($traveler?->booking->booking_code),
                    ])->columns(3),

                Section::make('Skema Pengembalian Dana')
                    ->schema([
                        Select::make('strategy')
                            ->label('Pilih Skema Refund')
                            ->options([
                                'full' => '100% Refund (Penuh)',
                                'standard' => '75% Refund (Potongan Admin 25%)',
                                'half' => '50% Refund (Potongan Admin 50%)',
                                'custom' => 'Nominal Kustom',
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set) use ($unitPrice) {
                                if ($state === 'full') $set('refund_amount', $unitPrice);
                                if ($state === 'standard') $set('refund_amount', $unitPrice * 0.75);
                                if ($state === 'half') $set('refund_amount', $unitPrice * 0.50);
                            }),
                        TextInput::make('refund_amount')
                            ->label('Nominal Refund (IDR)')
                            ->numeric()
                            ->required()
                            ->dehydrated(true)
                            ->helperText('Jumlah yang akan dikirim kembali ke rekening peserta.')
                            ->readOnly(fn ($get) => $get('strategy') !== 'custom'),
                        Textarea::make('reason')
                            ->label('Catatan / Alasan')
                            ->placeholder('Sakit mendadak / Pembatalan jadwal oleh peserta')
                            ->required(),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $formData = $this->form->getState();
        $traveler = $this->getTravelerProperty();
        $booking = $traveler->booking;

        DB::transaction(function () use ($traveler, $booking, $formData) {
            // Recalculate manually to be absolutely sure we don't save 0
            $unitPrice = ($booking->total_amount + (float)$booking->refund_amount) / $booking->pax;
            $refundAmount = (float)($formData['refund_amount'] ?? 0);
            
            // Backup logic in case Filament state fails
            if ($refundAmount <= 0) {
                if ($formData['strategy'] === 'full') $refundAmount = $unitPrice;
                if ($formData['strategy'] === 'standard') $refundAmount = $unitPrice * 0.75;
                if ($formData['strategy'] === 'half') $refundAmount = $unitPrice * 0.50;
            }

            // 1. Update Booking Header
            // We subtract from total_amount and add to refund_amount
            $booking->update([
                'total_amount' => max(0, $booking->total_amount - $refundAmount),
                'refund_amount' => (float)$booking->refund_amount + $refundAmount,
                'follow_up_status' => 'refund_processed',
                'follow_up_note' => ($booking->follow_up_note ? $booking->follow_up_note . "\n" : "") . 
                                  "REFUND PESERTA {$traveler->name} — " . $formData['reason'] . " (Rp " . number_format($refundAmount) . ")",
            ]);

            // 2. Mark Traveler as Cancelled
            $traveler->update([
                'status' => 'cancelled'
            ]);
        });

        Notification::make()
            ->title('Berhasil Memproses Refund')
            ->success()
            ->send();

        $this->redirect(RefundBookingResource::getUrl('index'));
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
