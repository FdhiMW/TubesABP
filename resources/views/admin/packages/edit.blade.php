@extends('admin.layouts.admin')

@section('title', 'Edit Paket')

@section('admin_content')

<div style="margin-bottom:25px;">
    <a href="{{ route('admin.packages.index') }}" style="color:#0b3120; text-decoration:none; font-size:14px;">
        ← Kembali ke daftar paket
    </a>
    <h1 style="margin:10px 0 0; color:#0b3120; font-size:28px; font-family:Georgia, serif;">✏️ Edit: {{ $package->name }}</h1>
</div>

@if($package->is_active)
    <div style="background:#fef3c7; color:#92400e; padding:14px 18px; border-radius:6px; margin-bottom:20px; border-left:4px solid #f59e0b;">
        <strong>⚠ Paket sedang AKTIF.</strong> Anda harus menonaktifkan paket dulu sebelum bisa mengedit detail-nya.
        <form action="{{ route('admin.packages.toggle', $package->id) }}" method="POST" style="margin-top:10px;">
            @csrf
            <button type="submit" style="background:#f59e0b; color:white; border:none; padding:8px 16px; border-radius:4px; cursor:pointer; font-weight:bold; font-size:13px;">
                Nonaktifkan Paket Sekarang
            </button>
        </form>
    </div>
@endif

@if($errors->any())
    <div style="background:#fee2e2; color:#991b1b; padding:14px 18px; border-radius:6px; margin-bottom:20px; border-left:4px solid #dc3545;">
        <strong style="display:block; margin-bottom:6px;">Mohon perbaiki:</strong>
        <ul style="margin:0; padding-left:20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.packages.update', $package->id) }}" method="POST"
      style="background:#fff; padding:30px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.05); {{ $package->is_active ? 'opacity:0.6; pointer-events:none;' : '' }}">
    @csrf
    @method('PUT')

    <div style="display:grid; grid-template-columns:2fr 1fr; gap:20px; margin-bottom:18px;">
        <div>
            <label style="display:block; margin-bottom:8px; color:#0b3120; font-weight:600; font-size:14px;">
                Nama Paket *
            </label>
            <input type="text" name="name" value="{{ old('name', $package->name) }}" required maxlength="100"
                   style="width:100%; padding:12px 14px; border:1px solid #e0d8cc; border-radius:6px; font-size:14px; box-sizing:border-box;">
        </div>
        <div>
            <label style="display:block; margin-bottom:8px; color:#0b3120; font-weight:600; font-size:14px;">
                Urutan Tampil
            </label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $package->sort_order) }}" min="0"
                   style="width:100%; padding:12px 14px; border:1px solid #e0d8cc; border-radius:6px; font-size:14px; box-sizing:border-box;">
        </div>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:18px;">
        <div>
            <label style="display:block; margin-bottom:8px; color:#0b3120; font-weight:600; font-size:14px;">
                Harga (Rupiah) *
            </label>
            <input type="number" name="price" value="{{ old('price', $package->price) }}" min="0" required step="1000"
                   style="width:100%; padding:12px 14px; border:1px solid #e0d8cc; border-radius:6px; font-size:14px; box-sizing:border-box;">
        </div>
        <div>
            <label style="display:block; margin-bottom:8px; color:#0b3120; font-weight:600; font-size:14px;">
                Label Harga *
            </label>
            <input type="text" name="price_label" value="{{ old('price_label', $package->price_label) }}" required maxlength="50"
                   style="width:100%; padding:12px 14px; border:1px solid #e0d8cc; border-radius:6px; font-size:14px; box-sizing:border-box;">
        </div>
    </div>

    <div style="margin-bottom:18px;">
        <label style="display:block; margin-bottom:8px; color:#0b3120; font-weight:600; font-size:14px;">
            Tagline
        </label>
        <input type="text" name="tagline" value="{{ old('tagline', $package->tagline) }}" maxlength="255"
               style="width:100%; padding:12px 14px; border:1px solid #e0d8cc; border-radius:6px; font-size:14px; box-sizing:border-box;">
    </div>

    <div style="margin-bottom:18px;">
        <label style="display:block; margin-bottom:8px; color:#0b3120; font-weight:600; font-size:14px;">
            Fitur Paket (satu per baris) *
        </label>
        <textarea name="features" rows="6" required
                  style="width:100%; padding:12px 14px; border:1px solid #e0d8cc; border-radius:6px; font-size:14px; box-sizing:border-box; font-family:inherit; resize:vertical;">{{ old('features', implode("\n", $package->features ?? [])) }}</textarea>
    </div>

    <div style="margin-bottom:18px;">
        <label style="display:block; margin-bottom:8px; color:#0b3120; font-weight:600; font-size:14px;">
            Warna Aksen
        </label>
        <div style="display:flex; gap:10px; align-items:center;">
            <input type="color" name="color" value="{{ old('color', $package->color) }}"
                   style="width:60px; height:42px; border:1px solid #e0d8cc; border-radius:6px; cursor:pointer;">
            <input type="text" value="{{ old('color', $package->color) }}" readonly
                   style="flex:1; padding:12px 14px; border:1px solid #e0d8cc; border-radius:6px; font-size:14px;"
                   id="colorPreview">
        </div>
    </div>

    <div style="background:#f9f7f5; padding:18px; border-radius:6px; margin-bottom:24px;">
        <h4 style="margin:0 0 14px; color:#0b3120; font-size:14px;">Pengaturan Status</h4>

        <label style="display:flex; align-items:center; gap:10px; cursor:pointer; margin-bottom:10px;">
            <input type="checkbox" name="is_popular" value="1" {{ old('is_popular', $package->is_popular) ? 'checked' : '' }}
                   style="width:18px; height:18px; cursor:pointer;">
            <span style="color:#374151; font-size:14px;">⭐ Popular</span>
        </label>

        <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $package->is_active) ? 'checked' : '' }}
                   style="width:18px; height:18px; cursor:pointer;">
            <span style="color:#374151; font-size:14px;">✓ Aktifkan paket</span>
        </label>
    </div>

    <div style="display:flex; gap:10px;">
        <a href="{{ route('admin.packages.index') }}"
           style="flex:1; padding:14px; text-align:center; border:1px solid #8a8a8a; color:#8a8a8a; text-decoration:none; border-radius:6px; font-weight:bold;">
            Batal
        </a>
        <button type="submit"
                style="flex:2; padding:14px; background:#0b3120; color:white; border:none; border-radius:6px; font-weight:bold; cursor:pointer; font-size:14px;"
                {{ $package->is_active ? 'disabled' : '' }}>
            💾 Update Paket
        </button>
    </div>
</form>

<script>
    document.querySelector('input[name="color"]')?.addEventListener('input', function(e) {
        document.getElementById('colorPreview').value = e.target.value;
    });
</script>

@endsection