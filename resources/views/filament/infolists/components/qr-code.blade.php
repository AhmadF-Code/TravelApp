@php
    $record = is_callable($getRecord) ? $getRecord() : $getRecord;
@endphp

<div>
    <div style="padding: 1rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; display: inline-block; background: white;">
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=MANUAL_PAY_{{ $record->booking_code }}_{{ $record->total_amount }}"
             alt="QR Code Status Pembayaran"
             style="width: 200px; height: 200px;" />
    </div>
    <div style="margin-top: 1rem; font-size: 0.875rem; color: #4b5563;">
        Minta pelanggan Anda men-scan QR ini, atau gunakan Kode Booking pesanan (<b>{{ $record->booking_code }}</b>) saat transfer manual sebesar <b>Rp {{ number_format($record->total_amount, 0, ',', '.') }}</b>.
    </div>
</div>
