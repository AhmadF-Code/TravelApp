<?php

namespace App\Filament\Resources\RefundBookingResource\Pages;

use App\Filament\Resources\RefundBookingResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewRefundBooking extends ViewRecord
{
    protected static string $resource = RefundBookingResource::class;

    protected static ?string $title = 'Detail Transaksi Refund (Complete)';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Transaksi Refund')
                    ->icon('heroicon-o-check-circle')
                    ->schema([
                        Infolists\Components\Grid::make(3)->schema([
                            Infolists\Components\TextEntry::make('booking_code')
                                ->label('Kode Booking')
                                ->weight('bold'),
                            Infolists\Components\TextEntry::make('customer_name')
                                ->label('Nama Pelanggan'),
                            Infolists\Components\TextEntry::make('refund_amount')
                                ->label('Total Refund')
                                ->money('IDR'),
                        ]),

                        Infolists\Components\Grid::make(2)->schema([
                            Infolists\Components\TextEntry::make('refund_completed_at')
                                ->label('Tanggal Refund Selesai')
                                ->dateTime()
                                ->color('success')
                                ->weight('bold'),
                            Infolists\Components\TextEntry::make('refundProcessor.name')
                                ->label('Diproses Oleh Admin')
                                ->badge()
                                ->color('info'),
                        ]),

                        Infolists\Components\ImageEntry::make('refund_proof_image')
                            ->label('Bukti Transaksi Uploaded')
                            ->columnSpanFull()
                            ->square(),
                    ]),
            ]);
    }
}
