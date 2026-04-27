@extends('layouts.app')

@section('title', 'Form Booking - Pendopo UTI')

@section('content')
<!-- Navigation Bar -->
<nav style="background:#f5f1ed; padding:20px 0; border-bottom:1px solid #e8e0d8; font-family: Georgia, serif;">
    <div style="max-width:1100px; margin:0 auto; display:flex; justify-content:flex-end; align-items:center; gap:40px; padding:0 20px;">
        <a href="{{ url('/') }}" style="color:#8a8a8a; text-decoration:none; font-size:16px; font-weight:500;">Home</a>
        <a href="#" style="color:#8a8a8a; text-decoration:none; font-size:16px; font-weight:500;">Facilities</a>
        <a href="{{ url('/booking#') }}" style="color:#0b3120; text-decoration:none; font-size:16px; font-weight:500;">Booking</a>
        <a href="{{ route('manage.index') }}" style="color:#8a8a8a; text-decoration:none; font-size:16px; font-weight:500;">Manage</a>

        {{-- Link Admin Panel — hanya muncul kalau role admin --}}
        <x-admin-link />
    </div>
</nav>

<div style="min-height:100vh; background:#f5f1ed; font-family: Georgia, serif; padding:40px 20px;">
    <div style="max-width:1100px; margin:0 auto; display:flex; gap:40px;">

        <!-- Left column: Form Card -->
        <div style="flex:1; max-width:500px;">
            <!-- Decorative flowers on far left -->
            <div style="position:fixed; left:20px; top:150px; width:160px; height:360px; background-image:url('{{ asset('asset/images/flowerbg1.png') }}'); background-size:cover; background-position:center; z-index:0; pointer-events:none;"></div>

            <!-- Form Card -->
            <div style="background:#fff; border-radius:16px; padding:40px; box-shadow:0 8px 32px rgba(0,0,0,0.1); position:relative; z-index:1;">
                
                <!-- Progress Indicator -->
                <div id="progressContainer" style="margin-bottom:48px;">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px; margin-bottom:16px;">
                        <div style="text-align:center; flex:1;">
                            <div style="width:56px; height:56px; margin:0 auto 12px; background:#c9a861; color:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:18px;">1</div>
                            <p style="font-size:13px; color:#0b3120; margin:0; font-weight:600;">Data Diri</p>
                        </div>
                        <div style="flex:1; height:2px; background:#d9d9d9; margin-top:28px;"></div>
                        <div style="text-align:center; flex:1;">
                            <div style="width:56px; height:56px; margin:0 auto 12px; background:#d9d9d9; color:#999; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:18px;">2</div>
                            <p style="font-size:13px; color:#999; margin:0; font-weight:500;">Detail<br>Acara</p>
                        </div>
                        <div style="flex:1; height:2px; background:#d9d9d9; margin-top:28px;"></div>
                        <div style="text-align:center; flex:1;">
                            <div style="width:56px; height:56px; margin:0 auto 12px; background:#d9d9d9; color:#999; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:18px;">3</div>
                            <p style="font-size:13px; color:#999; margin:0; font-weight:500;">Paket</p>
                        </div>
                        <div style="flex:1; height:2px; background:#d9d9d9; margin-top:28px;"></div>
                        <div style="text-align:center; flex:1;">
                            <div style="width:56px; height:56px; margin:0 auto 12px; background:#d9d9d9; color:#999; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:18px;">4</div>
                            <p style="font-size:13px; color:#999; margin:0; font-weight:500;">Konfirmasi</p>
                        </div>
                    </div>
                </div>

                @if ($errors->any())
                    <div style="color:red;">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('booking.store') }}" method="POST" id="bookingForm">
                    @csrf
                    <input type="hidden" name="venue_id" id="step" value="1">

                    <!-- Step 1: Isi Data Diri -->
                    <div id="step1" class="form-step">
                        <h2 style="text-align:center; margin:0 0 24px 0; font-size:28px; color:#0b3120;">Isi Data Diri</h2>
                        <p style="text-align:center; color:#6a6a6a; margin:0 0 24px 0; font-size:14px;">Masukkan informasi lengkap Anda untuk memulai booking pernikahan.</p>

                        <div style="margin-bottom:16px;">
                            <label style="display:block; margin-bottom:8px; color:#0b3120; font-weight:600;">Nama Pengantin</label>
                            <input type="text" value="{{ $user->name }}" name="bride_groom_name" placeholder="Andi & Salabila" style="width:100%; padding:12px 16px; border:1px solid #ddd; border-radius:8px; font-size:14px; box-sizing:border-box;" readonly>
                        </div>

                        <div style="margin-bottom:16px;">
                            <label style="display:block; margin-bottom:8px; color:#0b3120; font-weight:600;">Email</label>
                            <input type="email" value="{{ $user->email }}" name="email" placeholder="andi.salsa@gmail.com" style="width:100%; padding:12px 16px; border:1px solid #ddd; border-radius:8px; font-size:14px; box-sizing:border-box;" readonly>
                        </div>

                        <div style="margin-bottom:24px;">
                            <label style="display:block; margin-bottom:8px; color:#0b3120; font-weight:600;">No Telepon / WhatsApp</label>
                            <input type="tel" value="{{ $user->phone }}" name="phone" placeholder="+62 812-9876-5432" style="width:100%; padding:12px 16px; border:1px solid #ddd; border-radius:8px; font-size:14px; box-sizing:border-box;" readonly>
                        </div>
                    </div>

                    <!-- Step 2: Isi Detail Acara -->
                    <div id="step2" class="form-step" style="display:none;">
                        <h2 style="text-align:center; margin:0 0 24px 0; font-size:28px; color:#0b3120;">Isi Detail Acara</h2>
                        <p style="text-align:center; color:#6a6a6a; margin:0 0 24px 0; font-size:14px;">Atur detail acara pernikahan Anda di bawah ini.</p>

                        <div style="margin-bottom:16px;">
                            <label style="display:block; margin-bottom:8px; color:#0b3120; font-weight:600;">Tanggal Pernikahan</label>
                            <input type="date" name="event_date" style="width:100%; padding:12px 16px; border:1px solid #ddd; border-radius:8px; font-size:14px; box-sizing:border-box;" required>
                        </div>

                        <div style="margin-bottom:16px;">
                            <label style="display:block; margin-bottom:8px; color:#0b3120; font-weight:600;">Waktu Acara</label>
                            <input type="time" name="event_time" min="07:00" max="22:00" value="10:00 AM" style="width:100%; padding:12px 16px; border:1px solid #ddd; border-radius:8px; font-size:14px; box-sizing:border-box;" required>
                        </div>

                        <div style="margin-bottom:16px;">
                            <label style="display:block; margin-bottom:8px; color:#0b3120; font-weight:600;">Waktu Acara Berakhir</label>
                            <input type="time" name="end_time" min="07:00" max="22:00" value="10:00 AM" style="width:100%; padding:12px 16px; border:1px solid #ddd; border-radius:8px; font-size:14px; box-sizing:border-box;" required>
                        </div>

                        <div style="margin-bottom:16px;">
                            <label style="display:block; margin-bottom:8px; color:#0b3120; font-weight:600;">Jumlah Tamu</label>
                            <input type="number" name="guest_count" placeholder="300" style="width:100%; padding:12px 16px; border:1px solid #ddd; border-radius:8px; font-size:14px; box-sizing:border-box;" required>
                        </div>
                    </div>

                    <!-- Step 3: Pilih Paket -->
                    <div id="step3" class="form-step" style="display:none;">
                        <h2 style="text-align:center; margin:0 0 24px 0; font-size:28px; color:#0b3120;">Pilih Paket</h2>
                        <p style="text-align:center; color:#6a6a6a; margin:0 0 24px 0; font-size:14px;">Silakan pilih paket pernikahan sesuai kebutuhan Anda.</p>

                        <div style="display:grid; grid-template-columns:1fr; gap:16px; margin-bottom:24px;">
                            <label style="display:block; padding:16px; border:2px solid #ddd; border-radius:12px; cursor:pointer; transition: all 0.3s;">
                                <input type="radio" name="package" value="Basic Package" style="margin-right:8px;"> <strong>Basic Package</strong><br>
                                <span style="display:block; color:#c9a861; font-size:20px; margin-top:8px; font-weight:700;">Rp 25jt</span>
                                <ul style="font-size:12px; color:#666; margin:8px 0 0 24px;">
                                    <li>✓ Dekorasi dasar</li>
                                    <li>✓ Catering 100 pax</li>
                                    <li>✓ Dokumentasi foto</li>
                                </ul>
                            </label>

                            <label style="display:block; padding:16px; border:2px solid #c9a861; border-radius:12px; background:#fffbf8; cursor:pointer; box-shadow:0 4px 12px rgba(201,168,97,0.15);">
                                <strong style="color:#c9a861;">✓ POPULER</strong><br>
                                <input type="radio" name="package" value="Premium Package" checked style="margin-right:8px;"> <strong>Premium Package</strong><br>
                                <span style="display:block; color:#c9a861; font-size:20px; margin-top:8px; font-weight:700;">Rp 40jt</span>
                                <ul style="font-size:12px; color:#666; margin:8px 0 0 24px;">
                                    <li>✓ Dekorasi full</li>
                                    <li>✓ Catering 200 pax</li>
                                    <li>✓ Foto & video</li>
                                </ul>
                            </label>

                            <label style="display:block; padding:16px; border:2px solid #ddd; border-radius:12px; cursor:pointer; transition: all 0.3s;">
                                <input type="radio" name="package" value="Luxury Package" style="margin-right:8px;"> <strong>Luxury Package</strong><br>
                                <span style="display:block; color:#c9a861; font-size:20px; margin-top:8px; font-weight:700;">Rp 60jt</span>
                                <ul style="font-size:12px; color:#666; margin:8px 0 0 24px;">
                                    <li>✓ Dekorasi eksklusif</li>
                                    <li>✓ Catering 500 pax</li>
                                    <li>✓ Panggung & hiburan</li>
                                </ul>
                            </label>
                        </div>

                        <p style="text-align:center; font-size:12px; color:#999;">Langkah 3 dari 4</p>
                    </div>

                    <!-- Step 4: Konfirmasi & Pembayaran -->
                    <div id="step4" class="form-step" style="display:none;">
                        <h2 style="text-align:center; margin:0 0 24px 0; font-size:28px; color:#0b3120;">Konfirmasi & Pembayaran</h2>
                        <p style="text-align:center; color:#6a6a6a; margin:0 0 24px 0; font-size:14px;">Tingau kembali pesanan Anda dan pilih metode pembayaran.</p>

                        <div style="background:#f9f7f5; padding:16px; border-radius:12px; margin-bottom:16px;">
                            <h4 style="margin:0 0 12px 0; color:#0b3120;">RINGKASAN PESANAN</h4>
                            <div style="display:flex; justify-content:space-between; margin-bottom:8px; font-size:14px;">
                                <span>🏛 Garden Wedding Jakarta Pusat</span><span>—</span>
                            </div>
                            <div style="display:flex; justify-content:space-between; margin-bottom:8px; font-size:14px;">
                                <span>📅 8 Juni 2025</span><span>—</span>
                            </div>
                            <div style="display:flex; justify-content:space-between; margin-bottom:8px; font-size:14px;">
                                <span>👥 Tamu: 300 orang</span><span>—</span>
                            </div>
                            <div style="display:flex; justify-content:space-between; margin-bottom:0; font-size:14px;">
                                <span>💎 Premium Package</span><span>—</span>
                            </div>
                        </div>

                        <div style="background:#fff9f2; padding:16px; border-radius:12px; margin-bottom:20px;">
                            <div style="display:flex; justify-content:space-between; margin-bottom:8px; border-bottom:1px solid #e8e0d8; padding-bottom:8px;">
                                <span>Subtotal</span><span>Rp 44.000.000</span>
                            </div>
                            <div style="display:flex; justify-content:space-between; margin-bottom:8px; border-bottom:1px solid #e8e0d8; padding-bottom:8px;">
                                <span>Pajak</span><span>Rp 5.000.000</span>
                            </div>
                            <div style="display:flex; justify-content:space-between; font-weight:700; font-size:16px; color:#0b3120;">
                                <span>Total</span><span>Rp 48.400.000</span>
                            </div>
                        </div>

                        <h4 style="margin:0 0 12px 0; color:#0b3120;">METODE PEMBAYARAN</h4>
                        <div style="margin-bottom:12px;">
                            <label style="display:block; padding:12px; border:1px solid #ddd; border-radius:8px; margin-bottom:8px; cursor:pointer;">
                                <input type="radio" name="payment_method" value="Transfer Bank" checked style="margin-right:8px;"> 
                                <strong>💰 Transfer Bank</strong><br>
                                <span style="font-size:12px; color:#666;">BCA · BNI · Mandiri</span>
                            </label>
                            <label style="display:block; padding:12px; border:1px solid #ddd; border-radius:8px; cursor:pointer;">
                                <input type="radio" name="payment_method" value="E-Wallet" style="margin-right:8px;"> 
                                <strong>💳 E-Wallet</strong><br>
                                <span style="font-size:12px; color:#666;">GoPay · OVO · Dana</span>
                            </label>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div style="display:flex; gap:12px; margin-top:32px;">
                        <button type="button" onclick="handleBackButton()" style="flex:1; padding:12px 16px; border:1px solid #c9a861; background:#fff; color:#c9a861; border-radius:8px; font-weight:700; cursor:pointer; font-size:14px;">← Kembali</button>
                        <button type="button" onclick="nextStep()" id="nextBtn" style="flex:1; padding:12px 16px; background:#c9a861; color:#fff; border:none; border-radius:8px; font-weight:700; cursor:pointer; font-size:14px;">Lanjut →</button>
                    </div>

                    <p style="text-align:center; font-size:12px; color:#999; margin-top:8px;">
                        <span id="stepIndicator">Langkah 1 dari 4</span>
                    </p>
                </form>
            </div>
        </div>

        <!-- Right column: Couple Photo -->
        <div style="flex:1; display:flex; align-items:center; justify-content:center;">
            <div style="width:100%; max-width:500px; aspect-ratio:3/4;">
                <img src="{{ asset('asset/images/examwedding.png') }}" alt="Wedding Couple" style="width:100%; height:100%; object-fit:cover; border-radius:24px; box-shadow:0 16px 48px rgba(0,0,0,0.15);">
            </div>
        </div>
    </div>
</div>

<script>
let currentStep = 1;

function showStep(step) {
    document.querySelectorAll('.form-step').forEach(el => el.style.display = 'none');
    document.getElementById('step' + step).style.display = 'block';

    const stepLabels = ['Data Diri', 'Detail Acara'];

    const progressHTML = `
        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px; margin-bottom:16px;">
            ${[1, 2].map(i => `
                <div style="text-align:center; flex:1;">
                    <div style="width:56px; height:56px; margin:0 auto 12px; background:${i === step ? '#c9a861' : '#d9d9d9'}; color:${i === step ? '#fff' : '#999'}; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:18px;">${i}</div>
                    <p style="font-size:13px; color:${i === step ? '#0b3120' : '#999'}; margin:0; font-weight:${i === step ? '600' : '500'};">${stepLabels[i-1]}</p>
                </div>
                ${i < 2 ? `<div style="flex:1; height:2px; background:#d9d9d9; margin-top:28px;"></div>` : ''}
            `).join('')}
        </div>
    `;
    document.getElementById('progressContainer').innerHTML = progressHTML;

    document.getElementById('stepIndicator').textContent = `Langkah ${step} dari 2`;

    const nextBtn = document.getElementById('nextBtn');

    if (step === 2) {
        nextBtn.textContent = '✓ Booking Sekarang →';
    } else {
        nextBtn.textContent = 'Lanjut →';
    }

    window.scrollTo(0, 0);
}

function nextStep() {
    if (currentStep === 2) {
        // 🔥 SUBMIT DI STEP 2
        document.getElementById('bookingForm').submit();
        return;
    }

    currentStep++;
    showStep(currentStep);
}

function previousStep() {
    if (currentStep > 1) {
        currentStep--;
        showStep(currentStep);
    }
}

function handleBackButton() {
    if (currentStep === 1) {
        window.location.href = '{{ url('/booking#') }}';
    } else {
        previousStep();
    }
}

showStep(1);
// JS DIBAWAH INI JANGAN DIHAPUS, INI BUAT 4 STEP
/*
let currentStep = 1;

function showStep(step) {
    document.querySelectorAll('.form-step').forEach(el => el.style.display = 'none');
    document.getElementById('step' + step).style.display = 'block';
    document.getElementById('step').value = step;
    
    // Update progress indicator
    const stepLabels = ['Data Diri', 'Detail Acara', 'Paket', 'Konfirmasi'];
    const progressHTML = `
        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px; margin-bottom:16px;">
            ${[1, 2, 3, 4].map(i => `
                <div style="text-align:center; flex:1;">
                    <div style="width:56px; height:56px; margin:0 auto 12px; background:${i === step ? '#c9a861' : '#d9d9d9'}; color:${i === step ? '#fff' : '#999'}; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:18px;">${i}</div>
                    <p style="font-size:13px; color:${i === step ? '#0b3120' : '#999'}; margin:0; font-weight:${i === step ? '600' : '500'};">${stepLabels[i-1].replace(' ', '<br>')}</p>
                </div>
                ${i < 4 ? `<div style="flex:1; height:2px; background:#d9d9d9; margin-top:28px;"></div>` : ''}
            `).join('')}
        </div>
    `;
    document.getElementById('progressContainer').innerHTML = progressHTML;
    
    // Update step indicator
    document.getElementById('stepIndicator').textContent = `Langkah ${step} dari 4`;
    
    // Update next button
    const nextBtn = document.getElementById('nextBtn');
    if (step === 4) {
        nextBtn.textContent = '✓ Konfirmasi & Booking →';
        nextBtn.type = 'submit';
    } else {
        nextBtn.textContent = 'Lanjut →';
        nextBtn.type = 'button';
    }

    // Scroll to top
    window.scrollTo(0, 0);
}

function nextStep() {
    if (currentStep < 4) {
        currentStep++;
        showStep(currentStep);
    }
}

function previousStep() {
    if (currentStep > 1) {
        currentStep--;
        showStep(currentStep);
    }
}

function handleBackButton() {
    if (currentStep === 1) {
        // Go back to booking page
        window.location.href = '{{ route('booking.form') }}';
    } else {
        previousStep();
    }
}

// Initialize
showStep(1);
*/
</script>

@endsection
