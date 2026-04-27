@extends('admin.layouts.admin')

@section('title', 'Kelola Booking - Admin')

@section('admin_content')
<div>
    <h1 style="margin:0 0 5px; color:#0b3120; font-size:32px;">Kelola Booking</h1>
    <p style="margin:0 0 25px; color:#8a8a8a;">Daftar semua booking dari user</p>

    @php
        $filters = [
            'pending'           => 'Pending',
            'awaiting_payment'  => 'Menunggu Bayar',
            'paid'              => 'Sudah Bayar',
            'confirmed'         => 'Confirmed',
            'cancelled'         => 'Cancelled',
            'completed'         => 'Selesai',
            'all'               => 'Semua',
        ];
    @endphp

    <div style="display:flex; gap:8px; margin-bottom:25px; flex-wrap:wrap;">
        @foreach($filters as $key => $label)
            <a href="{{ route('admin.bookings.index', ['status' => $key]) }}"
               style="padding:8px 16px; border-radius:20px; text-decoration:none; font-size:13px;
                      {{ $status === $key
                            ? 'background:#0b3120; color:white;'
                            : 'background:white; color:#0b3120; border:1px solid #e8e0d8;' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div style="background:white; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.05); overflow:hidden;">
        @if($bookings->isEmpty())
            <p style="color:#8a8a8a; text-align:center; padding:60px 0;">Tidak ada booking pada status ini.</p>
        @else
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="background:#f5f1ed;">
                        <th style="padding:14px; text-align:left; font-size:13px; color:#0b3120;">Kode Booking</th>
                        <th style="padding:14px; text-align:left; font-size:13px; color:#0b3120;">User</th>
                        <th style="padding:14px; text-align:left; font-size:13px; color:#0b3120;">Venue</th>
                        <th style="padding:14px; text-align:left; font-size:13px; color:#0b3120;">Tanggal & Jam</th>
                        <th style="padding:14px; text-align:right; font-size:13px; color:#0b3120;">Total</th>
                        <th style="padding:14px; text-align:center; font-size:13px; color:#0b3120;">Status</th>
                        <th style="padding:14px; text-align:right; font-size:13px; color:#0b3120;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $b)
                        <tr style="border-top:1px solid #e8e0d8;">
                            <td style="padding:14px; font-family:monospace; font-size:13px;">{{ $b->booking_code }}</td>
                            <td style="padding:14px;">
                                {{ $b->user->name }}<br>
                                <small style="color:#8a8a8a;">{{ $b->user->email }}</small>
                            </td>
                            <td style="padding:14px;">{{ $b->venue->name ?? '-' }}</td>
                            <td style="padding:14px;">
                                {{ $b->event_date->format('d M Y') }}<br>
                                <small style="color:#8a8a8a;">{{ $b->event_time }} - {{ $b->end_time }}</small>
                            </td>
                            <td style="padding:14px; text-align:right; font-weight:bold;">
                                Rp {{ number_format($b->total_price, 0, ',', '.') }}
                            </td>
                            <td style="padding:14px; text-align:center;">
                                @include('admin.bookings._status_badge', ['status' => $b->status])
                            </td>
                            <td style="padding:14px; text-align:right;">
                                <a href="{{ route('admin.bookings.show', $b->id) }}"
                                   style="background:#0b3120; color:white; padding:6px 14px; text-decoration:none; border-radius:4px; font-size:13px;">Detail</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="padding:20px;">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection