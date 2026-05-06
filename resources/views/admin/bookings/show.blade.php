@extends('admin.layouts.admin')

@section('title', 'Detail Booking - Admin')

@section('admin_content')
<div>
    <a href="{{ route('admin.bookings.index') }}"
       style="color:#8a8a8a; text-decoration:none; font-size:14px;">← Kembali ke Daftar</a>

    <h1 style="margin:10px 0 30px; color:#0b3120; font-size:32px;">Detail Booking</h1>

    <div style="display:grid; grid-template-columns:2fr 1fr; gap:25px;">

        {{-- ===== INFO BOOKING ===== --}}
        <div style="background:white; padding:30px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.05);">
            <div style="display:flex; justify-content:space-between; align-items:start; margin-bottom:25px; padding-bottom:20px; border-bottom:1px solid #e8e0d8;">
                <div>
                    <p style="margin:0 0 5px; color:#8a8a8a; font-size:13px;">Kode Booking</p>
                    <h2 style="margin:0; color:#0b3120; font-family:monospace;">{{ $booking->booking_code }}</h2>
                </div>
                @include('admin.bookings._status_badge', [
                    'status' => $booking->status,
                    'payment_status' => $booking->payment_status
                ])
            </div>

            <h3 style="margin:0 0 15px; color:#0b3120; font-size:16px;">📋 Detail Acara</h3>
            <table style="width:100%; margin-bottom:25px;">
                <tr><td style="padding:8px 0; color:#8a8a8a; width:180px;">Venue</td><td style="padding:8px 0;">{{ $booking->venue->name ?? '-' }}</td></tr>
                <tr><td style="padding:8px 0; color:#8a8a8a;">Lokasi</td><td style="padding:8px 0;">{{ $booking->venue->location ?? '-' }}</td></tr>
                <tr><td style="padding:8px 0; color:#8a8a8a;">Tanggal Mulai</td><td style="padding:8px 0;">{{ $booking->event_date->format('d F Y') }}</td></tr>
                <tr><td style="padding:8px 0; color:#8a8a8a;">Tanggal Selesai</td><td style="padding:8px 0;">{{ $booking->end_date?->format('d F Y') ?? '-' }}</td></tr>
                <tr><td style="padding:8px 0; color:#8a8a8a;">Jam</td><td style="padding:8px 0;">{{ $booking->event_time }} - {{ $booking->end_time }}</td></tr>
                <tr><td style="padding:8px 0; color:#8a8a8a;">Jumlah Tamu</td><td style="padding:8px 0;">{{ $booking->guest_count }} orang</td></tr>
                <tr><td style="padding:8px 0; color:#8a8a8a;">Total Biaya</td><td style="padding:8px 0; font-weight:bold; color:#0b3120; font-size:18px;">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td></tr>
            </table>

            <h3 style="margin:0 0 15px; color:#0b3120; font-size:16px;">👤 Data Pemesan</h3>
            <table style="width:100%; margin-bottom:25px;">
                <tr><td style="padding:8px 0; color:#8a8a8a; width:180px;">Nama</td><td style="padding:8px 0;">{{ $booking->user->name }}</td></tr>
                <tr><td style="padding:8px 0; color:#8a8a8a;">Email</td><td style="padding:8px 0;">{{ $booking->user->email }}</td></tr>
                <tr><td style="padding:8px 0; color:#8a8a8a;">Telepon</td><td style="padding:8px 0;">{{ $booking->user->phone ?? '-' }}</td></tr>
            </table>

            @if($booking->cancellation_reason)
                <h3 style="margin:0 0 10px; color:#dc3545; font-size:16px;">⚠️ Alasan Pembatalan</h3>
                <div style="background:#fee2e2; padding:15px; border-radius:6px; color:#991b1b;">
                    {{ $booking->cancellation_reason }}<br>
                    <small>Dibatalkan pada: {{ $booking->cancelled_at?->format('d M Y H:i') }}</small>
                </div>
            @endif
        </div>

        {{-- ===== PANEL AKSI ===== --}}
        <div>
            @if($booking->status === 'pending')
                <div style="background:white; padding:25px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.05); margin-bottom:20px;">
                    <h3 style="margin:0 0 10px; color:#0b3120;">✓ Setujui Booking</h3>
                    <p style="margin:0 0 15px; font-size:13px; color:#8a8a8a;">
                        Booking akan diteruskan ke tahap pembayaran. Booking lain di slot waktu yang sama akan otomatis ditolak.
                    </p>
                    <form method="POST" action="{{ route('admin.bookings.approve', $booking->id) }}"
                          onsubmit="return confirm('Yakin setujui booking ini?')">
                        @csrf
                        <button type="submit"
                                style="width:100%; padding:12px; background:#10b981; color:white; border:none; border-radius:6px; font-family:Georgia, serif; font-size:15px; cursor:pointer;">
                            Approve Booking
                        </button>
                    </form>
                </div>

                <div style="background:white; padding:25px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.05);">
                    <h3 style="margin:0 0 10px; color:#0b3120;">✗ Tolak Booking</h3>
                    <form method="POST" action="{{ route('admin.bookings.reject', $booking->id) }}">
                        @csrf
                        <textarea name="reason" required maxlength="500" rows="4"
                                  placeholder="Alasan penolakan (wajib diisi)"
                                  style="width:100%; padding:10px; border:1px solid #e8e0d8; border-radius:6px; font-family:Georgia, serif; font-size:13px; box-sizing:border-box; margin-bottom:10px;"></textarea>
                        <button type="submit"
                                onclick="return confirm('Yakin tolak booking ini?')"
                                style="width:100%; padding:12px; background:#dc3545; color:white; border:none; border-radius:6px; font-family:Georgia, serif; font-size:15px; cursor:pointer;">
                            Tolak Booking
                        </button>
                    </form>
                </div>

            @elseif($booking->status === 'awaiting_payment')
                <div style="background:#dbeafe; padding:25px; border-radius:10px; color:#1e40af;">
                    <h3 style="margin:0 0 10px;">💳 Menunggu Pembayaran</h3>
                    <p style="margin:0 0 15px; font-size:13px;">
                        Booking sudah disetujui. User sedang dalam proses pembayaran via Midtrans.
                    </p>
                    <form method="POST" action="{{ route('admin.bookings.reject', $booking->id) }}">
                        @csrf
                        <textarea name="reason" required maxlength="500" rows="3"
                                  placeholder="Alasan pembatalan"
                                  style="width:100%; padding:10px; border:1px solid #93c5fd; border-radius:6px; font-family:Georgia, serif; font-size:13px; box-sizing:border-box; margin-bottom:10px; background:white;"></textarea>
                        <button type="submit"
                                onclick="return confirm('Batalkan booking ini?')"
                                style="width:100%; padding:10px; background:#dc3545; color:white; border:none; border-radius:6px; font-family:Georgia, serif; cursor:pointer;">
                            Batalkan Booking
                        </button>
                    </form>
                </div>

            @elseif($booking->payment_status === 'paid')
                <div style="background:white; padding:25px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.05);">
                    <h3 style="margin:0 0 10px; color:#0b3120;">✓ Konfirmasi Final</h3>
                    <p style="margin:0 0 15px; font-size:13px; color:#8a8a8a;">
                        Pembayaran sudah diterima. Klik untuk konfirmasi final booking.
                    </p>
                    <form method="POST" action="{{ route('admin.bookings.confirm', $booking->id) }}">
                        @csrf
                        <button type="submit"
                                style="width:100%; padding:12px; background:#0b3120; color:white; border:none; border-radius:6px; font-family:Georgia, serif; font-size:15px; cursor:pointer;">
                            Konfirmasi Booking
                        </button>
                    </form>
                </div>

            @else
                <div style="background:white; padding:25px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.05); text-align:center;">
                    <p style="color:#8a8a8a; margin:0;">Tidak ada aksi tersedia untuk status ini.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection