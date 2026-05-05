@extends('layouts.app')

@section('title', 'Form Booking - Pendopo UTI')

@section('content')

{{-- ========== TOP NAVBAR ========== --}}
<nav style="background:#f5f1ed; padding:18px 0; border-bottom:1px solid #e8e0d8; font-family:Georgia, serif; position:sticky; top:0; z-index:100;">
    <div style="max-width:1200px; margin:0 auto; display:flex; justify-content:space-between; align-items:center; padding:0 30px;">
        <a href="{{ url('/') }}" style="text-decoration:none;">
            <span style="color:#0b3120; font-size:22px; font-weight:bold; letter-spacing:1px;">PENDOPO UTI</span>
        </a>
        <div style="display:flex; gap:35px; align-items:center;">
            <a href="{{ url('/') }}" style="color:#8a8a8a; text-decoration:none; font-size:15px;">Home</a>
            <a href="{{ url('/#facilities') }}" style="color:#8a8a8a; text-decoration:none; font-size:15px;">Facilities</a>
            <a href="{{ route('booking.create') }}" style="color:#0b3120; text-decoration:none; font-size:15px; font-weight:600;">Booking</a>
            <a href="{{ route('manage.index') }}" style="color:#8a8a8a; text-decoration:none; font-size:15px;">Manage</a>

            @auth
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}"
                       style="background:#d4af37; color:#0b3120; text-decoration:none; padding:8px 16px; border-radius:4px; font-size:14px; font-weight:bold;">
                        🛡️ Admin Panel
                    </a>
                @endif
                <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                    @csrf
                    <button type="submit" style="background:transparent; border:1px solid #8a8a8a; color:#8a8a8a; padding:8px 16px; border-radius:4px; cursor:pointer; font-family:Georgia, serif; font-size:14px;">
                        Logout
                    </button>
                </form>
            @endauth
        </div>
    </div>
</nav>

<div style="min-height:calc(100vh - 70px); background:#f5f1ed; font-family:Georgia, serif; padding:50px 20px;">
    <div style="max-width:760px; margin:0 auto;">

        <div style="background:#fff; border-radius:20px; padding:50px 50px 40px; box-shadow:0 8px 32px rgba(0,0,0,0.08);">

            {{-- Progress Indicator --}}
            <div id="progressContainer" style="margin-bottom:40px;"></div>

            @if($errors->any())
                <div style="background:#fee2e2; color:#991b1b; padding:14px 18px; border-radius:8px; border-left:4px solid #dc3545; margin-bottom:20px;">
                    <strong style="display:block; margin-bottom:6px;">Mohon perbaiki:</strong>
                    <ul style="margin:0; padding-left:20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('booking.store') }}" method="POST" id="bookingForm">
                @csrf
                <input type="hidden" name="venue_id" value="{{ $venue->id ?? 1 }}">

                {{-- ========== STEP 1: DATA DIRI ========== --}}
                <div id="step1" class="form-step">
                    <h2 style="text-align:center; margin:0 0 12px; font-size:32px; color:#0b3120; font-weight:normal;">Isi Data Diri</h2>
                    <p style="text-align:center; color:#8a8a8a; margin:0 0 30px; font-size:14px;">Masukkan informasi lengkap Anda untuk memulai booking pernikahan.</p>

                    <div style="margin-bottom:18px;">
                        <label style="display:block; margin-bottom:8px; color:#0b3120; font-weight:600; font-size:14px;">Nama Lengkap</label>
                        <input type="text" value="{{ $user->name }}" readonly
                               style="width:100%; padding:13px 16px; border:1px solid #e0d8cc; border-radius:8px; font-size:14px; box-sizing:border-box; background:#faf8f5; font-family:Georgia, serif;">
                    </div>

                    <div style="margin-bottom:18px;">
                        <label style="display:block; margin-bottom:8px; color:#0b3120; font-weight:600; font-size:14px;">Email</label>
                        <input type="email" value="{{ $user->email }}" readonly
                               style="width:100%; padding:13px 16px; border:1px solid #e0d8cc; border-radius:8px; font-size:14px; box-sizing:border-box; background:#faf8f5; font-family:Georgia, serif;">
                    </div>

                    <div style="margin-bottom:18px;">
                        <label style="display:block; margin-bottom:8px; color:#0b3120; font-weight:600; font-size:14px;">Nomor HP</label>
                        <input type="text" value="{{ $user->phone ?? '-' }}" readonly
                               style="width:100%; padding:13px 16px; border:1px solid #e0d8cc; border-radius:8px; font-size:14px; box-sizing:border-box; background:#faf8f5; font-family:Georgia, serif;">
                    </div>

                    <p style="font-size:12px; color:#8a8a8a; text-align:center; margin-top:8px;">
                        Data diri diambil dari akun Anda.
                    </p>
                </div>

                {{-- ========== STEP 2: DETAIL ACARA ========== --}}
                <div id="step2" class="form-step" style="display:none;">
                    <h2 style="text-align:center; margin:0 0 12px; font-size:32px; color:#0b3120; font-weight:normal;">Detail Acara</h2>
                    <p style="text-align:center; color:#8a8a8a; margin:0 0 30px; font-size:14px;">Atur detail acara pernikahan Anda di bawah ini.</p>

                    <div style="margin-bottom:18px;">
                        <label style="display:block; margin-bottom:8px; color:#0b3120; font-weight:600; font-size:14px;">Tanggal Pernikahan</label>
                        <input type="date" name="event_date" min="{{ date('Y-m-d') }}"
                               value="{{ old('event_date') }}" required
                               style="width:100%; padding:13px 16px; border:1px solid #e0d8cc; border-radius:8px; font-size:14px; box-sizing:border-box; font-family:Georgia, serif;">
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:18px;">
                        <div>
                            <label style="display:block; margin-bottom:8px; color:#0b3120; font-weight:600; font-size:14px;">Waktu Mulai</label>
                            <input type="time" name="event_time" min="07:00" max="22:00"
                                   value="{{ old('event_time', '10:00') }}" required
                                   style="width:100%; padding:13px 16px; border:1px solid #e0d8cc; border-radius:8px; font-size:14px; box-sizing:border-box; font-family:Georgia, serif;">
                        </div>
                        <div>
                            <label style="display:block; margin-bottom:8px; color:#0b3120; font-weight:600; font-size:14px;">Waktu Selesai</label>
                            <input type="time" name="end_time" min="07:00" max="22:00"
                                   value="{{ old('end_time', '17:00') }}" required
                                   style="width:100%; padding:13px 16px; border:1px solid #e0d8cc; border-radius:8px; font-size:14px; box-sizing:border-box; font-family:Georgia, serif;">
                        </div>
                    </div>

                    <div style="margin-bottom:18px;">
                        <label style="display:block; margin-bottom:8px; color:#0b3120; font-weight:600; font-size:14px;">Jumlah Tamu</label>
                        <input type="number" name="guest_count" min="1" max="500"
                               value="{{ old('guest_count') }}" placeholder="300" required
                               style="width:100%; padding:13px 16px; border:1px solid #e0d8cc; border-radius:8px; font-size:14px; box-sizing:border-box; font-family:Georgia, serif;">
                    </div>

                    <p style="font-size:12px; color:#8a8a8a; background:#fff7e6; padding:10px 14px; border-radius:6px; margin-top:16px;">
                        ⏰ Jam operasional venue: 07:00 - 22:00. Maksimal 2 booking per tanggal.
                    </p>
                </div>

                {{-- ========== STEP 3: PILIH PAKET ========== --}}
                <div id="step3" class="form-step" style="display:none;">
                    <h2 style="text-align:center; margin:0 0 12px; font-size:32px; color:#0b3120; font-weight:normal;">Pilih Paket</h2>
                    <p style="text-align:center; color:#8a8a8a; margin:0 0 30px; font-size:14px;">Silakan pilih paket pernikahan sesuai kebutuhan Anda.</p>

                    <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:14px; margin-bottom:20px;">
                        @foreach($packages as $key => $pkg)
                            <label class="pkg-label" style="cursor:pointer; display:block; position:relative;">
                                <input type="radio" name="package" value="{{ $key }}"
                                       {{ old('package', 'basic') === $key ? 'checked' : '' }}
                                       style="display:none;">

                                @if($pkg['is_popular'])
                                    <div style="position:absolute; top:-10px; left:50%; transform:translateX(-50%); background:#c9a861; color:white; padding:3px 12px; border-radius:10px; font-size:11px; font-weight:bold; z-index:2;">
                                        Popular
                                    </div>
                                @endif

                                <div class="pkg-card" style="border:2px solid #e8e0d8; border-radius:12px; padding:20px 16px; background:#fff; height:100%; box-sizing:border-box; text-align:center; transition:all 0.2s;">
                                    <p style="margin:0 0 6px; color:#8a8a8a; font-size:12px; font-weight:600;">{{ $pkg['name'] }}</p>
                                    <p style="margin:0 0 16px; color:{{ $pkg['color'] }}; font-size:26px; font-weight:bold;">{{ $pkg['price_label'] }}</p>

                                    <ul style="margin:0 0 18px; padding:0; list-style:none; text-align:left;">
                                        @foreach($pkg['features'] as $feature)
                                            <li style="padding:4px 0; font-size:12px; color:#444; display:flex; align-items:start; gap:6px;">
                                                <span style="color:{{ $pkg['color'] }}; flex-shrink:0;">✓</span>
                                                <span>{{ $feature }}</span>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <div class="pkg-btn"
                                         style="width:100%; padding:9px; background:transparent; color:{{ $pkg['color'] }}; border:1.5px solid {{ $pkg['color'] }}; border-radius:6px; font-family:Georgia, serif; font-size:13px; font-weight:bold; box-sizing:border-box;">
                                        <span class="pkg-btn-text">Pilih Paket</span>
                                        <span class="pkg-btn-selected" style="display:none;">✓ Dipilih</span>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    @error('package')
                        <p style="color:#dc3545; text-align:center; font-size:13px; margin:10px 0 0;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ========== STEP 4: KONFIRMASI ========== --}}
                <div id="step4" class="form-step" style="display:none;">
                    <h2 style="text-align:center; margin:0 0 12px; font-size:32px; color:#0b3120; font-weight:normal;">Konfirmasi Booking</h2>
                    <p style="text-align:center; color:#8a8a8a; margin:0 0 30px; font-size:14px;">Tinjau kembali detail pesanan Anda sebelum konfirmasi.</p>

                    <div style="background:#f9f7f5; padding:24px; border-radius:12px; margin-bottom:18px; border-left:4px solid #c9a861;">
                        <h4 style="margin:0 0 16px; color:#0b3120; font-size:14px; text-transform:uppercase; letter-spacing:1px;">Ringkasan Pesanan</h4>

                        <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #e8e0d8; font-size:14px;">
                            <span style="color:#8a8a8a;">🏛️ Venue</span>
                            <span style="color:#0b3120; font-weight:600;">{{ $venue->name ?? 'Pendopo Utama UTI' }}</span>
                        </div>
                        <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #e8e0d8; font-size:14px;">
                            <span style="color:#8a8a8a;">📅 Tanggal</span>
                            <span style="color:#0b3120; font-weight:600;" id="summary-date">-</span>
                        </div>
                        <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #e8e0d8; font-size:14px;">
                            <span style="color:#8a8a8a;">⏰ Jam</span>
                            <span style="color:#0b3120; font-weight:600;" id="summary-time">-</span>
                        </div>
                        <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #e8e0d8; font-size:14px;">
                            <span style="color:#8a8a8a;">👥 Jumlah Tamu</span>
                            <span style="color:#0b3120; font-weight:600;" id="summary-guest">-</span>
                        </div>
                        <div style="display:flex; justify-content:space-between; padding:8px 0; font-size:14px;">
                            <span style="color:#8a8a8a;">💎 Paket</span>
                            <span style="color:#0b3120; font-weight:600;" id="summary-pkg">-</span>
                        </div>
                    </div>

                    <div style="background:#fff9f2; padding:24px; border-radius:12px; margin-bottom:24px; border-left:4px solid #c9a861;">
                        <div style="display:flex; justify-content:space-between; align-items:center; font-size:18px; font-weight:bold; color:#0b3120;">
                            <span>TOTAL</span>
                            <span style="color:#c9a861; font-size:24px;" id="summary-total">-</span>
                        </div>
                    </div>

                    <div style="background:#e0f2fe; padding:16px 20px; border-radius:10px; color:#0c4a6e; font-size:13px;">
                        <strong>💳 Cara Pembayaran:</strong>
                        <p style="margin:6px 0 0;">
                            Setelah booking dibuat, admin akan meninjau pesanan Anda. Setelah <strong>disetujui admin</strong>,
                            Anda dapat melakukan pembayaran via <strong>Midtrans</strong>.
                        </p>
                    </div>
                </div>

                {{-- Tombol Navigasi --}}
                <div style="display:flex; gap:12px; margin-top:32px;">
                    <button type="button" onclick="handleBackButton()"
                            style="flex:1; padding:14px; border:1.5px solid #c9a861; background:#fff; color:#c9a861; border-radius:8px; font-weight:bold; cursor:pointer; font-size:14px; font-family:Georgia, serif;">
                        ← Kembali
                    </button>
                    <button type="button" onclick="nextStep()" id="nextBtn"
                            style="flex:2; padding:14px; background:#c9a861; color:#fff; border:none; border-radius:8px; font-weight:bold; cursor:pointer; font-size:14px; font-family:Georgia, serif;">
                        Lanjut →
                    </button>
                </div>

                <p style="text-align:center; font-size:12px; color:#999; margin-top:16px;">
                    <span id="stepIndicator">Langkah 1 dari 4</span>
                </p>
            </form>
        </div>
    </div>
</div>

<style>
    .pkg-label input[type="radio"]:checked ~ .pkg-card {
        border-color: #c9a861 !important;
        background: #fffbf2 !important;
        box-shadow: 0 4px 16px rgba(201,168,97,0.2);
    }
    .pkg-label input[type="radio"]:checked ~ .pkg-card .pkg-btn {
        background: #c9a861;
        color: #fff;
    }
    .pkg-label input[type="radio"]:checked ~ .pkg-card .pkg-btn-text { display:none; }
    .pkg-label input[type="radio"]:checked ~ .pkg-card .pkg-btn-selected { display:inline !important; }
    .pkg-label:hover .pkg-card { transform:translateY(-2px); }
</style>

<script>
    const packages = @json($packages);
    let currentStep = 1;

    function renderProgress(step) {
        const labels = ['Data Diri', 'Detail Acara', 'Paket', 'Konfirmasi'];
        let html = '<div style="display:flex; justify-content:space-between; align-items:flex-start; gap:8px;">';

        for (let i = 1; i <= 4; i++) {
            const isDone   = i < step;
            const isActive = i === step;
            const bg       = (isDone || isActive) ? '#c9a861' : '#e0d8cc';
            const txtColor = (isDone || isActive) ? '#fff' : '#999';
            const labelColor = isActive ? '#0b3120' : (isDone ? '#c9a861' : '#999');
            const content  = isDone ? '✓' : i;

            html += `
                <div style="text-align:center; flex:1;">
                    <div style="width:48px; height:48px; margin:0 auto 8px; background:${bg}; color:${txtColor};
                                border-radius:50%; display:flex; align-items:center; justify-content:center;
                                font-weight:bold; font-size:16px;">
                        ${content}
                    </div>
                    <p style="font-size:12px; color:${labelColor}; margin:0; font-weight:${isActive ? '700' : '500'};">${labels[i-1]}</p>
                </div>
            `;
            if (i < 4) {
                const lineColor = i < step ? '#c9a861' : '#e0d8cc';
                html += `<div style="flex:0.6; height:2px; background:${lineColor}; margin-top:24px;"></div>`;
            }
        }
        html += '</div>';
        document.getElementById('progressContainer').innerHTML = html;
    }

    function showStep(step) {
        document.querySelectorAll('.form-step').forEach(el => el.style.display = 'none');
        document.getElementById('step' + step).style.display = 'block';
        renderProgress(step);
        document.getElementById('stepIndicator').textContent = `Langkah ${step} dari 4`;

        const nextBtn = document.getElementById('nextBtn');
        if (step === 4) {
            nextBtn.textContent = '✓ Konfirmasi Booking';
            updateSummary();
        } else {
            nextBtn.textContent = 'Lanjut →';
        }
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function validateStep(step) {
        if (step === 2) {
            const date  = document.querySelector('[name="event_date"]').value;
            const start = document.querySelector('[name="event_time"]').value;
            const end   = document.querySelector('[name="end_time"]').value;
            const guest = document.querySelector('[name="guest_count"]').value;
            if (!date || !start || !end || !guest) {
                alert('Lengkapi semua field detail acara.');
                return false;
            }
            if (start >= end) {
                alert('Waktu selesai harus setelah waktu mulai.');
                return false;
            }
        }
        if (step === 3) {
            const checked = document.querySelector('[name="package"]:checked');
            if (!checked) {
                alert('Pilih salah satu paket.');
                return false;
            }
        }
        return true;
    }

    function nextStep() {
        if (!validateStep(currentStep)) return;
        if (currentStep < 4) {
            currentStep++;
            showStep(currentStep);
        } else {
            document.getElementById('bookingForm').submit();
        }
    }

    function previousStep() {
        if (currentStep > 1) { currentStep--; showStep(currentStep); }
    }

    function handleBackButton() {
        if (currentStep === 1) window.location.href = '{{ route('booking.create') }}';
        else previousStep();
    }

    function updateSummary() {
        const date  = document.querySelector('[name="event_date"]').value;
        const start = document.querySelector('[name="event_time"]').value;
        const end   = document.querySelector('[name="end_time"]').value;
        const guest = document.querySelector('[name="guest_count"]').value;
        const pkg   = document.querySelector('[name="package"]:checked')?.value;

        document.getElementById('summary-date').textContent = date
            ? new Date(date).toLocaleDateString('id-ID', { day:'numeric', month:'long', year:'numeric' })
            : '-';
        document.getElementById('summary-time').textContent  = (start && end) ? `${start} - ${end} WIB` : '-';
        document.getElementById('summary-guest').textContent = guest ? `${guest} orang` : '-';

        if (pkg && packages[pkg]) {
            document.getElementById('summary-pkg').textContent   = packages[pkg].name;
            document.getElementById('summary-total').textContent = 'Rp ' + packages[pkg].price.toLocaleString('id-ID');
        }
    }

    showStep(1);
</script>

@endsection