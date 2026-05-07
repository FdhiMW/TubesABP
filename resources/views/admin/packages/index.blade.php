@extends('admin.layouts.admin')

@section('title', 'Manage Paket')

@section('admin_content')

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">
    <div>
        <h1 style="margin:0; color:#0b3120; font-size:28px; font-family:Georgia, serif;">📦 Manage Paket</h1>
        <p style="margin:5px 0 0; color:#8a8a8a; font-size:14px;">
            Kelola paket booking. Maksimal <strong>3 paket aktif</strong> yang bisa dipilih user.
        </p>
    </div>
    <a href="{{ route('admin.packages.create') }}"
       style="background:#0b3120; color:white; text-decoration:none; padding:12px 24px; border-radius:6px; font-weight:bold; font-size:14px;">
        + Tambah Paket
    </a>
</div>

{{-- Stats Bar --}}
<div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:15px; margin-bottom:25px;">
    <div style="background:#fff; padding:20px; border-radius:8px; border-left:4px solid #c9a861;">
        <p style="margin:0; color:#8a8a8a; font-size:13px;">Total Paket</p>
        <p style="margin:0; color:#0b3120; font-size:28px; font-weight:bold;">{{ $packages->total() }}</p>
    </div>
    <div style="background:#fff; padding:20px; border-radius:8px; border-left:4px solid {{ $activeCount >= 3 ? '#dc3545' : '#10b981' }};">
        <p style="margin:0; color:#8a8a8a; font-size:13px;">Paket Aktif</p>
        <p style="margin:0; color:#0b3120; font-size:28px; font-weight:bold;">
            {{ $activeCount }} / 3
        </p>
    </div>
    <div style="background:#fff; padding:20px; border-radius:8px; border-left:4px solid #6b7280;">
        <p style="margin:0; color:#8a8a8a; font-size:13px;">Slot Tersisa</p>
        <p style="margin:0; color:#0b3120; font-size:28px; font-weight:bold;">{{ max(0, 3 - $activeCount) }}</p>
    </div>
</div>

@if(session('success'))
    <div style="background:#d1fae5; color:#065f46; padding:12px 18px; border-radius:6px; margin-bottom:20px; border-left:4px solid #10b981;">
        ✓ {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div style="background:#fee2e2; color:#991b1b; padding:12px 18px; border-radius:6px; margin-bottom:20px; border-left:4px solid #dc3545;">
        @foreach($errors->all() as $error)
            <p style="margin:0;">⚠ {{ $error }}</p>
        @endforeach
    </div>
@endif

{{-- Tabel Paket --}}
<div style="background:#fff; border-radius:10px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.05);">
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="background:#f5f1ed;">
                <th style="padding:14px; text-align:left; font-size:13px; color:#0b3120;">No</th>
                <th style="padding:14px; text-align:left; font-size:13px; color:#0b3120;">Nama Paket</th>
                <th style="padding:14px; text-align:right; font-size:13px; color:#0b3120;">Harga</th>
                <th style="padding:14px; text-align:center; font-size:13px; color:#0b3120;">Popular</th>
                <th style="padding:14px; text-align:center; font-size:13px; color:#0b3120;">Status</th>
                <th style="padding:14px; text-align:right; font-size:13px; color:#0b3120;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($packages as $pkg)
                <tr style="border-top:1px solid #e8e0d8;">
                    <td style="padding:14px; color:#8a8a8a;">{{ $loop->iteration + ($packages->currentPage() - 1) * $packages->perPage() }}</td>
                    <td style="padding:14px;">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div style="width:8px; height:32px; background:{{ $pkg->color }}; border-radius:2px;"></div>
                            <div>
                                <p style="margin:0; color:#0b3120; font-weight:bold;">{{ $pkg->name }}</p>
                                @if($pkg->tagline)
                                    <p style="margin:2px 0 0; color:#8a8a8a; font-size:12px;">{{ $pkg->tagline }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td style="padding:14px; text-align:right; font-weight:bold; color:#0b3120;">
                        {{ $pkg->price_label }}
                        <p style="margin:2px 0 0; color:#8a8a8a; font-size:11px; font-weight:normal;">
                            Rp {{ number_format($pkg->price, 0, ',', '.') }}
                        </p>
                    </td>
                    <td style="padding:14px; text-align:center;">
                        @if($pkg->is_popular)
                            <span style="background:#fef3c7; color:#92400e; padding:4px 10px; border-radius:12px; font-size:11px; font-weight:bold;">
                                ⭐ Popular
                            </span>
                        @else
                            <span style="color:#cbd5e1;">-</span>
                        @endif
                    </td>
                    <td style="padding:14px; text-align:center;">
                        <form action="{{ route('admin.packages.toggle', $pkg->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit"
                                    style="background:{{ $pkg->is_active ? '#d1fae5' : '#f3f4f6' }};
                                           color:{{ $pkg->is_active ? '#065f46' : '#6b7280' }};
                                           padding:6px 14px; border-radius:12px; border:none;
                                           font-size:11px; font-weight:bold; cursor:pointer;">
                                {{ $pkg->is_active ? '✓ Active' : '○ Inactive' }}
                            </button>
                        </form>
                    </td>
                    <td style="padding:14px; text-align:right;">
                        <a href="{{ route('admin.packages.edit', $pkg->id) }}"
                           style="background:#3b82f6; color:white; text-decoration:none; padding:6px 12px; border-radius:4px; font-size:12px; margin-right:5px;">
                            ✏️ Edit
                        </a>
                        <form action="{{ route('admin.packages.destroy', $pkg->id) }}" method="POST" style="display:inline;"
                              onsubmit="return confirm('Yakin hapus paket ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    style="background:#dc3545; color:white; border:none; padding:6px 12px; border-radius:4px; font-size:12px; cursor:pointer;">
                                🗑 Hapus
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="padding:40px; text-align:center; color:#8a8a8a;">
                        Belum ada paket. <a href="{{ route('admin.packages.create') }}" style="color:#0b3120;">Tambahkan paket pertama →</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:20px;">
    {{ $packages->links() }}
</div>

@endsection