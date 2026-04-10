<?php

namespace App\Filament\Resources\FollowUpBookingResource\Pages;

use App\Filament\Resources\FollowUpBookingResource;
use App\Models\Booking;
use App\Models\Schedule;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Contracts\HasForms;

class ListFollowUpBookingsStable extends Page implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    protected static string $resource = FollowUpBookingResource::class;

    protected static string $view = 'filament.resources.follow-up-booking-resource.pages.list-follow-up-bookings-stable';

    protected static ?string $title = 'Follow-up Task (Stable Mode)';

    public string $search = '';

    public function mount(): void
    {
        $this->search = request()->query('search', '');
    }

    public function getRecordsProperty(): LengthAwarePaginator
    {
        $query = Booking::query()
            ->with(['schedule.trip'])
            ->latest('updated_at');

        $status = request()->query('status', 'all');
        if ($status !== 'all') {
            $query->where('follow_up_status', $status);
        } else {
            // Default show needs_follow_up
             $query->where('follow_up_status', 'needs_follow_up');
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('booking_code', 'like', "%{$this->search}%")
                  ->orWhere('customer_name', 'like', "%{$this->search}%");
            });
        }

        return $query->paginate(15);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function openReschedule(int $bookingId): void
    {
        $this->mountAction('pindah_jadwal', ['record_id' => $bookingId]);
    }

    public function openCancel(int $bookingId): void
    {
        $this->mountAction('cancel_refund', ['record_id' => $bookingId]);
    }

    protected function getActions(): array
    {
        return [
            // Reschedule Action
            Action::make('pindah_jadwal')
                ->label('Pindah Jadwal')
                ->icon('heroicon-o-arrows-right-left')
                ->color('success')
                ->modalHeading('Pindah Keberangkatan')
                ->form([
                    Forms\Components\Select::make('new_schedule_id')
                        ->label('Jadwal Baru')
                        ->options(function ($arguments) {
                           if (!isset($arguments['record_id'])) return [];
                           $booking = Booking::find($arguments['record_id']);
                           if(!$booking) return [];
                           return Schedule::where('trip_id', $booking->schedule->trip_id)
                                ->where('id', '!=', $booking->schedule_id)
                                ->where('status', 'active')
                                ->get()
                                ->mapWithKeys(function ($s) {
                                    $booked = $s->bookings()->whereIn('status', ['paid', 'pending', 'confirmed'])->sum('pax');
                                    $remaining = max(0, $s->quota - $booked);
                                    return [$s->id => \Carbon\Carbon::parse($s->departure_date)->format('d M Y') . " (Sisa: {$remaining})"];
                                });
                        })
                        ->required(),
                    Forms\Components\Textarea::make('note')->label('Catatan')->required(),
                ])
                ->action(function (array $data, array $arguments) {
                    $booking = Booking::findOrFail($arguments['record_id']);
                    $newSchedule = Schedule::findOrFail($data['new_schedule_id']);
                    
                    $booked = $newSchedule->bookings()->whereIn('status', ['paid', 'pending', 'confirmed'])->sum('pax');
                    $remaining = $newSchedule->quota - $booked;
                    
                    if ($remaining < $booking->pax) {
                        Notification::make()->title('Kuota gagal')->danger()->send();
                        return;
                    }

                    $booking->update([
                        'schedule_id' => $data['new_schedule_id'],
                        'follow_up_status' => 'resolved_moved',
                        'follow_up_note' => $data['note'],
                    ]);

                    Notification::make()->title('Booking Berhasil Dipindahkan')->success()->send();
                }),

            // Cancel & Refund Action
            Action::make('cancel_refund')
                ->label('Batal & Refund')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->modalHeading('Batalkan & Refund')
                ->form([
                    Forms\Components\TextInput::make('refund_amount')
                        ->label('Jumlah Refund')
                        ->numeric()
                        ->required(),
                    Forms\Components\Textarea::make('note')->label('Alasan')->required(),
                ])
                ->action(function (array $data, array $arguments) {
                    $booking = Booking::findOrFail($arguments['record_id']);
                    $booking->update([
                        'status' => 'cancelled',
                        'follow_up_status' => 'refund_processed',
                        'follow_up_note' => $data['note'],
                        'refund_amount' => $data['refund_amount'],
                    ]);
                    Notification::make()->title('Booking telah dibatalkan & di-refund')->success()->send();
                }),
        ];
    }
}
