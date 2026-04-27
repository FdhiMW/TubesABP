@extends('admin.layouts.admin')

@section('title', 'Kelola Survey - Admin')

@section('admin_content')
<div>
    <h1 style="margin:0 0 5px; color:#0b3120; font-size:32px;">Kelola Survey</h1>
    <p style="margin:0 0 25px; color:#8a8a8a;">Daftar permintaan survey lokasi dari user</p>

    @php
        $filters = [
            'pending'   => 'Pending',
            'confirmed' => 'Confirmed',
            'completed' => 'Selesai',
            'cancelled' => 'Cancelled',
            'all'       => 'Semua',
        ];

        $colorMap = [
            'pending'   => ['#fef3c7', '#92400e'],
            'confirmed' => ['#d1fae5', '#065f46'],
            'completed' => ['#e0e7ff', '#3730a3'],
            'cancelled' => ['#fee2e2', '#991b1b'],
        ];
    @endphp

    <div style="display:flex; gap:8px; margin-bottom:25px; flex-wrap:wrap;">
        @foreach($filters as $key => $label)
            <a href="{{ route('admin.surveys.index', ['status' => $key]) }}"
               style="padding:8px 16px; border-radius:20px; text-decoration:none; font-size:13px;
                      {{ $status === $key
                            ? 'background:#0b3120; color:white;'
                            : 'background:white; color:#0b3120; border:1px solid #e8e0d8;' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div style="background:white; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.05); overflow:hidden;">
        @if($surveys->isEmpty())
            <p style="color:#8a8a8a; text-align:center; padding:60px 0;">Tidak ada survey pada status ini.</p>
        @else
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="background:#f5f1ed;">
                        <th style="padding:14px; text-align:left; font-size:13px; color:#0b3120;">User</th>
                        <th style="padding:14px; text-align:left; font-size:13px; color:#0b3120;">Venue</th>
                        <th style="padding:14px; text-align:left; font-size:13px; color:#0b3120;">Tanggal & Jam</th>
                        <th style="padding:14px; text-align:left; font-size:13px; color:#0b3120;">Catatan</th>
                        <th style="padding:14px; text-align:center; font-size:13px; color:#0b3120;">Status</th>
                        <th style="padding:14px; text-align:right; font-size:13px; color:#0b3120;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($surveys as $s)
                        @php [$bg, $fg] = $colorMap[$s->status] ?? ['#e5e7eb', '#374151']; @endphp
                        <tr style="border-top:1px solid #e8e0d8;">
                            <td style="padding:14px;">
                                {{ $s->user->name }}<br>
                                <small style="color:#8a8a8a;">{{ $s->user->email }}</small>
                            </td>
                            <td style="padding:14px;">{{ $s->venue->name ?? '-' }}</td>
                            <td style="padding:14px;">
                                {{ $s->proposed_date->format('d M Y') }}<br>
                                <small style="color:#8a8a8a;">{{ $s->proposed_time }} - {{ $s->end_time }}</small>
                            </td>
                            <td style="padding:14px; max-width:200px; font-size:13px; color:#555;">
                                {{ \Illuminate\Support\Str::limit($s->notes ?? '-', 50) }}
                            </td>
                            <td style="padding:14px; text-align:center;">
                                <span style="background:{{ $bg }}; color:{{ $fg }}; padding:4px 10px; border-radius:12px; font-size:12px; font-weight:bold;">
                                    {{ ucfirst($s->status) }}
                                </span>
                            </td>
                            <td style="padding:14px; text-align:right;">
                                <a href="{{ route('admin.surveys.show', $s->id) }}"
                                   style="background:#0b3120; color:white; padding:6px 14px; text-decoration:none; border-radius:4px; font-size:13px;">Detail</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="padding:20px;">
                {{ $surveys->links() }}
            </div>
        @endif
    </div>
</div>
@endsection