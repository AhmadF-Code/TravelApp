<?php

namespace App\Filament\Resources\FollowUpBookingResource\Pages;

use App\Filament\Resources\FollowUpBookingResource;
use App\Models\Schedule;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;

class RescheduleBooking extends EditRecord
{
    protected static string $resource = FollowUpBookingResource::class;

    protected static ?string $title = 'Reschedule Booking';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Booking')
                    ->description('Data pesanan saat ini yang akan dipindahkan.')
                    ->schema([
                        Forms\Components\TextInput::make('booking_code')
                            ->disabled()
                            ->label('Kode Booking'),
                        Forms\Components\TextInput::make('customer_name')
                            ->disabled()
                            ->label('Nama Pelanggan'),
                        Forms\Components\TextInput::make('current_trip')
                            ->label('Trip Saat Ini')
                            ->default(fn ($record) => optional($record->schedule->trip)->title)
                            ->disabled(),
                        Forms\Components\TextInput::make('pax')
                            ->disabled()
                            ->label('Jumlah Peserta'),
                    ])->columns(2),

                Forms\Components\Section::make('Pilih Jadwal Baru')
                    ->description('Silakan pilih tanggal keberangkatan baru untuk grup ini (harus dalam paket trip yang sama).')
                    ->schema([
                        Forms\Components\Select::make('schedule_id')
                            ->label('Jadwal Keberangkatan Baru')
                            ->required()
                            ->options(function ($record) {
                                return Schedule::where('trip_id', $record->schedule->trip_id)
                                    ->where('id', '!=', $record->schedule_id)
                                    ->where('status', 'active')
                                    ->get()
                                    ->mapWithKeys(function ($s) {
                                        $booked = $s->bookings()->whereIn('status', ['paid', 'pending', 'confirmed'])->sum('pax');
                                        $remaining = max(0, $s->quota - $booked);
                                        return [$s->id => $s->departure_date->format('d M Y') . " (Sisa: {$remaining} kursi)"];
                                    });
                            })
                            ->live()
                            ->afterStateUpdated(function ($state, $record, $set) {
                                if (!$state) return;
                                $newS = Schedule::find($state);
                                if ($newS) {
                                    $booked = $newS->bookings()->whereIn('status', ['paid', 'pending', 'confirmed'])->sum('pax');
                                    if (($newS->quota - $booked) < $record->pax) {
                                        Notification::make()
                                            ->title('Kuota Penuh')
                                            ->body('Jadwal pilihan tidak memiliki cukup kursi untuk jumlah pax ini.')
                                            ->danger()
                                            ->send();
                                        $set('schedule_id', null);
                                    }
                                }
                            }),
                        Forms\Components\Textarea::make('follow_up_note')
                            ->label('Catatan Alasan Reschedule')
                            ->required()
                            ->placeholder('Contoh: Pelanggan pindah ke keberangkatan bulan depan karena alasan kesehatan.'),
                    ]),
            ]);
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->label('Terapkan Reschedule')
                ->color('success'),
            $this->getCancelFormAction()
                ->label('Batal'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['follow_up_status'] = 'resolved_moved';
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
            ->title('Reschedule Berhasil')
            ->body('Peserta telah dipindahkan ke jadwal keberangkatan baru.');
    }
}
