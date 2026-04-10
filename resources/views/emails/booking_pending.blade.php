<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; background: #f9fafb; padding: 20px; color: #111827; }
        .box { background: white; padding: 30px; border-radius: 10px; max-width: 600px; margin: 0 auto; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .btn { display: inline-block; padding: 12px 24px; background: #2563eb; color: white; text-decoration: none; border-radius: 6px; font-weight: bold; margin-top: 20px; }
        .meta { background: #f3f4f6; padding: 15px; border-radius: 6px; margin: 20px 0; font-size: 14px; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Halo, {{ $booking->customer_name }}! 👋</h2>
        <p>Terima kasih telah melakukan pemesanan trip bersama kami. Saat ini status pesanan Anda adalah <strong>PENDING</strong> (Menunggu Pembayaran).</p>
        
        <div class="meta">
            <strong>Kode Pemesanan:</strong> {{ $booking->booking_code }}<br>
            <strong>Paket Trip:</strong> {{ $booking->trip->title }}<br>
            <strong>Tgl Keberangkatan:</strong> {{ \Carbon\Carbon::parse($booking->schedule->departure_date)->format('d M Y') }}<br>
            <strong>Jumlah Pax:</strong> {{ $booking->pax }} Orang<br>
            <strong>Total Tagihan:</strong> Rp {{ number_format($booking->total_amount, 0, ',', '.') }}
        </div>

        <p>Segera selesaikan pembayaran agar kursi Anda di jadwal tersebut segera terkunci (diamankan).</p>

        @if($booking->xendit_invoice_url)
            <a href="{{ $booking->xendit_invoice_url }}" class="btn">Bayar Sekarang via Xendit</a>
        @endif

        <p style="margin-top:30px;font-size:12px;color:#6b7280;">Anda juga bebas mengecek status pesanan ini kapan saja melalui website kami di menu Cek Pesanan.</p>
    </div>
</body>
</html>
