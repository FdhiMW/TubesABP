@extends('admin.layouts.admin')

@section('title', 'Detail Survey - Admin')

@section('admin_content')
<div>
    <a href="{{ route('admin.surveys.index') }}"
       style="color:#8a8a8a; text-decoration:none; font-size:14px;">← Kembali ke Daftar</a>

    <h1 style="margin:10px 0 30px; color:#0b3120; font-size:32px;">Detail Survey</h1>

    @php
        $colorMap = [
            'pending'   => ['#fef3c7', '#92400e'],
            'confirmed' => ['#d1fae5', '#065f46'],
            'completed' => ['#e0e7ff', '#3730a3'],
            'cancelled' => ['#fee2e2', '#991b1b'],
        ];
        [$bg, $fg] = $colorMap[$survey->status] ?? ['#e5e7eb', '#374151'];
    @endphp

    <div style="display:grid; grid-template-columns:2fr 1fr; gap:25px;">

        {{-- ===== INFO SURVEY ===== --}}
        <div style="background:white; padding:30px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.05);">
            <div style="display:flex; justify-content:space-between; align-items:start; margin-bottom:25px; padding-bottom:20px; border-bottom:1px solid #e8e0d8;">
                <div>
                    <p style="margin:0 0 5px; color:#8a8a8a; font-size:13px;">ID Survey</p>
                    <h2 style="margin:0; color:#0b3120;">#{{ $survey->id }}</h2>
                </div>
                <span style="background:{{ $bg }}; color:{{ $fg }}; padding:6px 14px; border-radius:12px; font-size:13px; font-weight:bold;">
                    {{ ucfirst($survey->status) }}
                </span>
            </div>

            <h3 style="margin:0 0 15px; color:#0b3120; font-size:16px;">📅 Detail Jadwal</h3>
            <table style="width:100%; margin-bottom:25px;">
                <tr><td style="padding:8px 0; color:#8a8a8a; width:200px;">Venue</td><td style="padding:8px 0;">{{ $survey->venue->name ?? '-' }}</td></tr>
                <tr><td style="padding:8px 0; color:#8a8a8a;">Lokasi</td><td style="padding:8px 0;">{{ $survey->venue->location ?? '-' }}</td></tr>
                <tr><td style="padding:8px 0; color:#8a8a8a;">Tanggal Diajukan</td><td style="padding:8px 0;">{{ $survey->proposed_date->format('d F Y') }}</td></tr>
                <tr><td style="padding:8px 0; color:#8a8a8a;">Jam Diajukan</td><td style="padding:8px 0;">{{ $survey->proposed_time }} - {{ $survey->end_time }}</td></tr>
                @if($survey->confirmed_date)
                    <tr><td style="padding:8px 0; color:#8a8a8a;">Tanggal Dikonfirmasi</td><td style="padding:8px 0; color:#10b981;">{{ \Carbon\Carbon::parse($survey->confirmed_date)->format('d F Y') }}</td></tr>
                    <tr><td style="padding:8px 0; color:#8a8a8a;">Jam Dikonfirmasi</td><td style="padding:8px 0; color:#10b981;">{{ $survey->confirmed_time }}</td></tr>
                @endif
            </table>

            @if($survey->notes)
                <h3 style="margin:0 0 10px; color:#0b3120; font-size:16px;">📝 Catatan dari User</h3>
                <div style="background:#f5f1ed; padding:15px; border-radius:6px; margin-bottom:25px;">
                    {{ $survey->notes }}
                </div>
            @endif

            <h3 style="margin:0 0 15px; color:#0b3120; font-size:16px;">👤 Data Pemohon</h3>
            <table style="width:100%; margin-bottom:15px;">
                <tr><td style="padding:8px 0; color:#8a8a8a; width:200px;">Nama</td><td style="padding:8px 0;">{{ $survey->user->name }}</td></tr>
                <tr><td style="padding:8px 0; color:#8a8a8a;">Email</td><td style="padding:8px 0;">{{ $survey->user->email }}</td></tr>
                <tr><td style="padding:8px 0; color:#8a8a8a;">Telepon</td><td style="padding:8px 0;">{{ $survey->user->phone ?? '-' }}</td></tr>
            </table>

            @if($survey->admin_notes)
                <h3 style="margin:20px 0 10px; color:#0b3120; font-size:16px;">💬 Catatan Admin</h3>
                <div style="background:#dbeafe; padding:15px; border-radius:6px; color:#1e40af;">
                    {{ $survey->admin_notes }}
                </div>
            @endif
        </div>

        {{-- ===== PANEL AKSI ===== --}}
        <div>
            @if($survey->status === 'pending')
                <div style="background:white; padding:25px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.05); margin-bottom:20px;">
                    <h3 style="margin:0 0 10px; color:#0b3120;">✓ Setujui Survey</h3>
                    <p style="margin:0 0 15px; font-size:13px; color:#8a8a8a;">
                        Konfirmasi jadwal survey sesuai yang diajukan user.
                    </p>
                    <form method="POST" action="{{ route('admin.surveys.approve', $survey->id) }}">
                        @csrf
                        <textarea name="admin_notes" maxlength="500" rows="3"
                                  placeholder="Catatan untuk user (opsional)"
                                  style="width:100%; padding:10px; border:1px solid #e8e0d8; border-radius:6px; font-family:Georgia, serif; font-size:13px; box-sizing:border-box; margin-bottom:10px;"></textarea>
                        <button type="submit"
                                onclick="return confirm('Yakin setujui survey ini?')"
                                style="width:100%; padding:12px; background:#10b981; color:white; border:none; border-radius:6px; font-family:Georgia, serif; font-size:15px; cursor:pointer;">
                            Approve Survey
                        </button>
                    </form>
                </div>

                <div style="background:white; padding:25px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.05);">
                    <h3 style="margin:0 0 10px; color:#0b3120;">✗ Tolak Survey</h3>
                    <form method="POST" action="{{ route('admin.surveys.reject', $survey->id) }}">
                        @csrf
                        <textarea name="admin_notes" required maxlength="500" rows="4"
                                  placeholder="Alasan penolakan (wajib diisi)"
                                  style="width:100%; padding:10px; border:1px solid #e8e0d8; border-radius:6px; font-family:Georgia, serif; font-size:13px; box-sizing:border-box; margin-bottom:10px;"></textarea>
                        <button type="submit"
                                onclick="return confirm('Yakin tolak survey ini?')"
                                style="width:100%; padding:12px; background:#dc3545; color:white; border:none; border-radius:6px; font-family:Georgia, serif; font-size:15px; cursor:pointer;">
                            Tolak Survey
                        </button>
                    </form>
                </div>

            @elseif($survey->status === 'confirmed')
                <div style="background:white; padding:25px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.05);">
                    <h3 style="margin:0 0 10px; color:#0b3120;">✓ Tandai Selesai</h3>
                    <p style="margin:0 0 15px; font-size:13px; color:#8a8a8a;">
                        Tandai bahwa survey sudah dilaksanakan.
                    </p>
                    <form method="POST" action="{{ route('admin.surveys.complete', $survey->id) }}">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('Tandai survey ini selesai?')"
                                style="width:100%; padding:12px; background:#0b3120; color:white; border:none; border-radius:6px; font-family:Georgia, serif; font-size:15px; cursor:pointer;">
                            Tandai Selesai
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