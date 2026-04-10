<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingTraveler;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function download(Request $request, string $type): StreamedResponse
    {
        $filename = match($type) {
            'sales'    => 'Sales_Report_' . now()->format('Y-m-d') . '.csv',
            'cancel'   => 'Cancel_Report_' . now()->format('Y-m-d') . '.csv',
            'refund'   => 'Refund_Report_' . now()->format('Y-m-d') . '.csv',
            'manifest' => 'Manifest_' . now()->format('Y-m-d') . '.csv',
            default    => 'Report.csv',
        };

        return response()->streamDownload(function () use ($type) {
            $handle = fopen('php://output', 'w');

            match($type) {
                'sales' => $this->writeSales($handle),
                'cancel' => $this->writeCancel($handle),
                'refund' => $this->writeRefund($handle),
                'manifest' => $this->writeManifest($handle),
                default => null,
            };

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function writeSales($handle): void
    {
        fputcsv($handle, ['Booking Code', 'Customer', 'Trip Package', 'Pax', 'Total Paid (IDR)', 'Tanggal Transaksi']);
        Booking::query()
            ->where('status', 'paid')
            ->with('schedule.trip')
            ->orderByDesc('id')
            ->chunk(500, function ($rows) use ($handle) {
                foreach ($rows as $b) {
                    fputcsv($handle, [
                        $b->booking_code,
                        $b->customer_name,
                        optional($b->schedule?->trip)->title,
                        $b->pax,
                        $b->total_amount,
                        $b->created_at->format('d-m-Y H:i'),
                    ]);
                }
            });
    }

    private function writeCancel($handle): void
    {
        fputcsv($handle, ['Booking Code', 'Customer', 'Phone', 'Alasan', 'Refund Diberikan', 'Tanggal Batal']);
        Booking::query()
            ->where('status', 'cancelled')
            ->orderByDesc('updated_at')
            ->chunk(500, function ($rows) use ($handle) {
                foreach ($rows as $b) {
                    fputcsv($handle, [
                        $b->booking_code,
                        $b->customer_name,
                        $b->customer_phone,
                        $b->follow_up_note,
                        $b->refund_amount,
                        $b->updated_at->format('d-m-Y'),
                    ]);
                }
            });
    }

    private function writeRefund($handle): void
    {
        fputcsv($handle, ['Booking Code', 'Customer', 'Jumlah Refund (IDR)', 'Catatan Audit', 'Tgl Diproses']);
        Booking::query()
            ->where('refund_amount', '>', 0)
            ->orderByDesc('updated_at')
            ->chunk(500, function ($rows) use ($handle) {
                foreach ($rows as $b) {
                    fputcsv($handle, [
                        $b->booking_code,
                        $b->customer_name,
                        $b->refund_amount,
                        $b->follow_up_note,
                        $b->updated_at->format('d-m-Y'),
                    ]);
                }
            });
    }

    private function writeManifest($handle): void
    {
        fputcsv($handle, ['Traveler ID', 'Nama Peserta', 'No. Telepon', 'Group Booking', 'Trip Package', 'Tgl Keberangkatan', 'Status']);
        BookingTraveler::query()
            ->with(['booking.schedule.trip'])
            ->orderByDesc('id')
            ->chunk(500, function ($rows) use ($handle) {
                foreach ($rows as $t) {
                    fputcsv($handle, [
                        $t->traveler_code,
                        $t->name,
                        $t->phone,
                        $t->booking?->booking_code,
                        optional($t->booking?->schedule?->trip)->title,
                        optional($t->booking?->schedule)?->departure_date?->format('d-m-Y'),
                        $t->status,
                    ]);
                }
            });
    }
}
