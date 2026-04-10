<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; background: #f0fdf4; padding: 20px; color: #111827; }
        .box { background: white; padding: 30px; border-radius: 10px; max-width: 600px; margin: 0 auto; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .btn { display: inline-block; padding: 12px 24px; background: #16a34a; color: white; text-decoration: none; border-radius: 6px; font-weight: bold; margin-top: 20px; text-align: center; }
        .meta { background: #f0fdf4; border: 1.5px solid #bbf7d0; padding: 15px; border-radius: 6px; margin: 20px 0; font-size: 14px; }
        .trip-img { width: 100%; height: auto; border-radius: 8px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="box">
        <h2 style="color:#15803d;">Invoice Lunas & Tiket Perjalanan ✅</h2>
        <p>Halo, <strong>{{ $booking->customer_name }}</strong>!</p>
        <p>Terima kasih. Pembayaran Anda untuk pesanan dengan kode <strong>{{ $booking->booking_code }}</strong> telah kami terima (LUNAS).</p>
        
        <img src="{{ $booking->trip->image_url }}" class="trip-img" alt="Trip">

        <div class="meta">
            <strong>Rincian Pesanan:</strong><br><br>
            <strong>Kode Booking:</strong> {{ $booking->booking_code }}<br>
            <strong>Tujuan Trip:</strong> {{ $booking->trip->title }}<br>
            <strong>Tgl Keberangkatan:</strong> {{ \Carbon\Carbon::parse($booking->schedule->departure_date)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($booking->schedule->return_date)->format('d M Y') }}<br>
            <strong>Daftar Penumpang / Kuota:</strong> {{ $booking->pax }} Orang<br>
            <strong>Nominal Dibayar:</strong> Rp {{ number_format($booking->total_amount, 0, ',', '.') }}<br>
        </div>

        <p>Anda bisa melihat jadwal itenerary lengkap dan detail persiapan rute tujuan langsung di portal pesanan kami.</p>

        <a href="{{ route('booking.show', $booking->booking_code) }}" class="btn">Lihat Panduan Perjalanan Saya</a>

        <p style="margin-top:30px;font-size:12px;color:#6b7280;">Simpan email ini sebagai tanda terima sah dan sertakan kode pesanan saat menghubungi Tour Guide.</p>
    </div>
</body>
</html>
