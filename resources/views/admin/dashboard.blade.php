@extends('admin.layouts.admin')

@section('title', 'Admin Dashboard - Pendopo UTI')

@section('admin_content')
<div>
    <h1 style="margin:0 0 5px; color:#0b3120; font-size:32px;">Dashboard</h1>
    <p style="margin:0 0 30px; color:#8a8a8a;">Ringkasan aktivitas Pendopo UTI</p>

    {{-- ===== KARTU STATISTIK ===== --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:20px; margin-bottom:40px;">

        @php
            $cards = [
                ['label' => 'Total User',          'value' => $stats['total_users'],         'color' => '#0b3120'],
                ['label' => 'Total Venue',         'value' => $stats['total_venues'],        'color' => '#0b3120'],
                ['label' => 'Booking Pending',     'value' => $stats['pending_bookings'],    'color' => '#f59e0b'],
                ['label' => 'Menunggu Bayar',      'value' => $stats['awaiting_payment'],    'color' => '#3b82f6'],
                ['label' => 'Sudah Bayar',         'value' => $stats['paid_bookings'],       'color' => '#10b981'],
                ['label' => 'Booking Confirmed',   'value' => $stats['confirmed_bookings'],  'color' => '#10b981'],
                ['label' => 'Survey Pending',      'value' => $stats['pending_surveys'],     'color' => '#f59e0b'],
                ['label' => 'Survey Confirmed',    'value' => $stats['confirmed_surveys'],   'color' => '#10b981'],
            ];
        @endphp

        @foreach($cards as $c)
            <div style="background:white; padding:25px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.05); border-top:3px solid {{ $c['color'] }};">
                <p style="margin:0 0 8px; color:#8a8a8a; font-size:13px; text-transform:uppercase; letter-spacing:1px;">{{ $c['label'] }}</p>
                <p style="margin:0; color:{{ $c['color'] }}; font-size:36px; font-weight:bold;">{{ $c['value'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- ===== BOOKING PENDING TERBARU ===== --}}
    <div style="background:white; padding:30px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.05); margin-bottom:30px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h2 style="margin:0; color:#0b3120; font-size:22px;">🏛️ Booking Pending Terbaru</h2>
            <a href="{{ route('admin.bookings.index') }}"
               style="color:#0b3120; text-decoration:none; font-size:14px;">Lihat Semua →</a>
        </div>

        @if($recentPendingBookings->isEmpty())
            <p style="color:#8a8a8a; text-align:center; padding:30px 0;">Tidak ada booking pending.</p>
        @else
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="background:#f5f1ed;">
                        <th style="padding:12px; text-align:left; font-size:13px; color:#0b3120;">Kode</th>
                        <th style="padding:12px; text-align:left; font-size:13px; color:#0b3120;">User</th>
                        <th style="padding:12px; text-align:left; font-size:13px; color:#0b3120;">Tanggal Acara</th>
                        <th style="padding:12px; text-align:left; font-size:13px; color:#0b3120;">Total</th>
                        <th style="padding:12px; text-align:right; font-size:13px; color:#0b3120;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentPendingBookings as $b)
                        <tr style="border-bottom:1px solid #e8e0d8;">
                            <td style="padding:12px; font-family:monospace; font-size:13px;">{{ $b->booking_code }}</td>
                            <td style="padding:12px;">{{ $b->user->name }}</td>
                            <td style="padding:12px;">
                                {{ $b->event_date->format('d M Y') }}<br>
                                <small style="color:#8a8a8a;">{{ $b->event_time }} - {{ $b->end_time }}</small>
                            </td>
                            <td style="padding:12px;">Rp {{ number_format($b->total_price, 0, ',', '.') }}</td>
                            <td style="padding:12px; text-align:right;">
                                <a href="{{ route('admin.bookings.show', $b->id) }}"
                                   style="background:#0b3120; color:white; padding:6px 14px; text-decoration:none; border-radius:4px; font-size:13px;">Detail</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- ===== SURVEY PENDING TERBARU ===== --}}
    <div style="background:white; padding:30px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.05);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h2 style="margin:0; color:#0b3120; font-size:22px;">📅 Survey Pending Terbaru</h2>
            <a href="{{ route('admin.surveys.index') }}"
               style="color:#0b3120; text-decoration:none; font-size:14px;">Lihat Semua →</a>
        </div>

        @if($recentPendingSurveys->isEmpty())
            <p style="color:#8a8a8a; text-align:center; padding:30px 0;">Tidak ada survey pending.</p>
        @else
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="background:#f5f1ed;">
                        <th style="padding:12px; text-align:left; font-size:13px; color:#0b3120;">User</th>
                        <th style="padding:12px; text-align:left; font-size:13px; color:#0b3120;">Venue</th>
                        <th style="padding:12px; text-align:left; font-size:13px; color:#0b3120;">Tanggal Survey</th>
                        <th style="padding:12px; text-align:right; font-size:13px; color:#0b3120;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentPendingSurveys as $s)
                        <tr style="border-bottom:1px solid #e8e0d8;">
                            <td style="padding:12px;">{{ $s->user->name }}</td>
                            <td style="padding:12px;">{{ $s->venue->name ?? '-' }}</td>
                            <td style="padding:12px;">
                                {{ $s->proposed_date->format('d M Y') }}<br>
                                <small style="color:#8a8a8a;">{{ $s->proposed_time }} - {{ $s->end_time }}</small>
                            </td>
                            <td style="padding:12px; text-align:right;">
                                <a href="{{ route('admin.surveys.show', $s->id) }}"
                                   style="background:#0b3120; color:white; padding:6px 14px; text-decoration:none; border-radius:4px; font-size:13px;">Detail</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection