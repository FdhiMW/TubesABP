@extends('layouts.app')

@section('title', 'Form Booking Survey - Pendopo UTI')

@section('content')
<!-- Navigation Bar -->
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
            @else
                <a href="{{ route('login') }}" style="color:#8a8a8a; text-decoration:none; font-size:15px;">Login</a>
            @endauth
        </div>
    </div>
</nav>

<div style="min-height:100vh; background:linear-gradient(90deg,#fbf6ef 0%, #ffffff 60%); font-family: Georgia, serif;">
    <div style="display:flex;">

        <!-- Left column: Form Card -->
        <div style="width:50%; height:100vh; position:relative; padding-left:36px; display:flex; align-items:center; overflow:hidden;">
            <!-- Decorative flowers on far left -->
            <div style="position:absolute; left:-80px; top:50%; transform:translateY(-50%); width:260px; height:520px; background-image:url('{{ asset('asset/images/flowerbg1.png') }}'); background-size:cover; background-position:center left; transform:translateX(0); z-index:0; pointer-events:none; filter:drop-shadow(0 10px 20px rgba(0,0,0,0.12));"></div>

            <!-- Form Card -->
            <div style="background:#fff; border-radius:16px; padding:30px; box-shadow:0 8px 32px rgba(0,0,0,0.1); position:relative; z-index:1; width:100%; max-width:450px; max-height:90vh; overflow-y:auto;">
                
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
                            <p style="font-size:13px; color:#999; margin:0; font-weight:500;">Detail<br>Survey</p>
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

                <form action="{{ route('survey.store') }}" method="POST" id="bookingForm">
                    @csrf
                    <input type="hidden" name="venue_id" value="1">

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
                            <label style="display:block; margin-bottom:8px; color:#0b3120; font-weight:600;">Tanggal Survey</label>
                            <input type="date" name="proposed_date" style="width:100%; padding:12px 16px; border:1px solid #ddd; border-radius:8px; font-size:14px; box-sizing:border-box;" required>
                        </div>

                        <div style="margin-bottom:16px;">
                            <label style="display:block; margin-bottom:8px; color:#0b3120; font-weight:600;">Waktu Survey</label>
                            <input type="time" name="proposed_time" min="07:00" max="22:00" value="10:00 AM" style="width:100%; padding:12px 16px; border:1px solid #ddd; border-radius:8px; font-size:14px; box-sizing:border-box;" required>
                        </div>

                        <div style="margin-bottom:16px;">
                            <label style="display:block; margin-bottom:8px; color:#0b3120; font-weight:600;">Catatan (Opsional)</label>
                            <textarea name="notes" placeholder="Catatan" style="width:100%; padding:12px 16px; border:1px solid #ddd; border-radius:8px; font-size:14px; box-sizing:border-box;"></textarea>
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

        <!-- Right column (large image) -->
        <div style="width:50%; height:100vh; overflow:hidden; position:relative;">
            <img src="{{ asset('asset/images/examwedding.png') }}" alt="Wedding Couple" style="width:100%; height:100%; object-fit:cover; display:block; filter:contrast(1.02) saturate(1.05);" />
            <!-- white gradient fade on left to mimic design -->
            <div style="position:absolute; left:0; top:0; bottom:0; width:40%; background:linear-gradient(90deg, rgba(255,255,255,1) 0%, rgba(255,255,255,0.2) 100%);"></div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    let currentStep = 1;

    function showStep(step) {
        // Sembunyikan semua step
        document.querySelectorAll('.form-step').forEach(el => el.style.display = 'none');

        const current = document.getElementById('step' + step);
        if (current) {
            current.style.display = 'block';
        }

        const stepLabels = ['Data Diri', 'Detail Acara'];

        // Update progress
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

        const progressContainer = document.getElementById('progressContainer');
        if (progressContainer) {
            progressContainer.innerHTML = progressHTML;
        }

        // Update step indicator (SAFE)
        const indicator = document.getElementById('stepIndicator');
        if (indicator) {
            indicator.textContent = `Langkah ${step} dari 2`;
        }

        // Update tombol next
        const nextBtn = document.getElementById('nextBtn');
        if (nextBtn) {
            if (step === 2) {
                nextBtn.textContent = '✓ Booking Sekarang →';
            } else {
                nextBtn.textContent = 'Lanjut →';
            }
        }

        window.scrollTo(0, 0);
    }

    window.nextStep = function () {
        console.log("NEXT DIKLIK"); // debug

        if (currentStep === 2) {
            const form = document.getElementById('bookingForm');
            if (form) form.submit();
            return;
        }

        currentStep++;
        showStep(currentStep);
    }

    window.previousStep = function () {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    }

    window.handleBackButton = function () {
        if (currentStep === 1) {
            window.location.href = '{{ url('/booking#') }}';
        } else {
            previousStep();
        }
    }

    // INIT
    showStep(1);
});
</script>