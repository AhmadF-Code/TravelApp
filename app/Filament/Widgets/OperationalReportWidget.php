<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\Widget;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;

class OperationalReportWidget extends Widget implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static ?int $sort = 0;
    protected int | string | array $columnSpan = 'half';
    protected static string $view = 'filament.widgets.operational-report-widget';

    public function downloadSalesReportAction(): Action
    {
        return Action::make('downloadSalesReport')
            ->label('Download Sales Report')
            ->icon('heroicon-m-banknotes')
            ->color('success')
            ->action(function () {
                return (new ExcelExport())
                    ->fromModel(Booking::class)
                    ->withFilename('Sales_Report_'.now()->format('Y-m-d'))
                    ->withQuery(Booking::query()->where('status', 'paid'))
                    ->withColumns([
                        Column::make('booking_code')->heading('Booking Code'),
                        Column::make('customer_name')->heading('Customer'),
                        Column::make('schedule.trip.title')->heading('Trip Package'),
                        Column::make('pax')->heading('Total Pax'),
                        Column::make('total_amount')->heading('Total Paid (IDR)'),
                        Column::make('created_at')->heading('Transaction Date'),
                    ])
                    ->download();
            });
    }

    public function downloadCancelReportAction(): Action
    {
        return Action::make('downloadCancelReport')
            ->label('Download Cancel Report')
            ->icon('heroicon-m-x-circle')
            ->color('danger')
            ->action(function () {
                return (new ExcelExport())
                    ->fromModel(Booking::class)
                    ->withFilename('Cancelled_Bookings_'.now()->format('Y-m-d'))
                    ->withQuery(Booking::query()->where('status', 'cancelled'))
                    ->withColumns([
                        Column::make('booking_code')->heading('Booking Code'),
                        Column::make('customer_name')->heading('Customer'),
                        Column::make('follow_up_note')->heading('Reason'),
                        Column::make('refund_amount')->heading('Refund Given'),
                        Column::make('updated_at')->heading('Cancellation Date'),
                    ])
                    ->download();
            });
    }

    public function downloadRefundReportAction(): Action
    {
        return Action::make('downloadRefundReport')
            ->label('Download Refund Report')
            ->icon('heroicon-m-arrow-path')
            ->color('warning')
            ->action(function () {
                return (new ExcelExport())
                    ->fromModel(Booking::class)
                    ->withFilename('Refund_Report_'.now()->format('Y-m-d'))
                    ->withQuery(Booking::query()->where('refund_amount', '>', 0))
                    ->withColumns([
                        Column::make('booking_code')->heading('Booking Code'),
                        Column::make('customer_name')->heading('Pax Representative'),
                        Column::make('refund_amount')->heading('Refund Amount'),
                        Column::make('follow_up_note')->heading('Refund Audit Note'),
                        Column::make('updated_at')->heading('Process Date'),
                    ])
                    ->download();
            });
    }

    public function downloadManifestAction(): Action
    {
        return Action::make('downloadManifest')
            ->label('Download Daily Manifest')
            ->icon('heroicon-m-users')
            ->color('info')
            ->action(function () {
                return (new ExcelExport())
                    ->fromModel(\App\Models\BookingTraveler::class)
                    ->withFilename('Passenger_Manifest_'.now()->format('Y-m-d'))
                    ->withQuery(\App\Models\BookingTraveler::query()->with(['booking.schedule.trip']))
                    ->withColumns([
                        Column::make('traveler_code')->heading('Traveler ID'),
                        Column::make('name')->heading('Passenger Name'),
                        Column::make('phone')->heading('Contact'),
                        Column::make('booking.booking_code')->heading('Group Code'),
                        Column::make('booking.schedule.trip.title')->heading('Trip Title'),
                        Column::make('status')->heading('Status'),
                    ])
                    ->download();
            });
    }
}
