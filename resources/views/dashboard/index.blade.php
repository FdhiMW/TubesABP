<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Pendopo Uti</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --gold: #B8860B;
            --gold-light: #D4A843;
            --gold-pale: #F5E6C8;
            --cream: #FDF8F0;
            --cream-dark: #F0E6D3;
            --charcoal: #2C2418;
            --brown: #5C4A32;
            --brown-light: #8B7355;
            --sage: #7A8B6F;
            --rose: #C9A9A6;
            --white: #FFFFFF;
            --error: #B44040;
            --success: #4A7A5B;
            --info: #4A6FA5;
            --font-display: 'Cormorant Garamond', Georgia, serif;
            --font-body: 'Jost', sans-serif;
        }

        body {
            font-family: var(--font-body);
            background: var(--cream);
            color: var(--charcoal);
            min-height: 100vh;
        }

        /* ── NAVBAR ── */
        .navbar {
            background: var(--white);
            border-bottom: 1px solid var(--cream-dark);
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 64px;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }

        .nav-brand-icon {
            width: 36px; height: 36px;
            border: 1.5px solid var(--gold);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, var(--gold-pale) 0%, var(--cream) 100%);
        }

        .nav-brand-icon svg { width: 18px; height: 18px; fill: var(--gold); }

        .nav-brand-text {
            font-family: var(--font-display);
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--charcoal);
        }

        .nav-brand-text em {
            font-style: italic;
            color: var(--gold);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            list-style: none;
        }

        .nav-links a {
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 400;
            color: var(--brown);
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: all 0.2s;
            letter-spacing: 0.02em;
        }

        .nav-links a:hover { background: var(--cream); color: var(--charcoal); }
        .nav-links a.active { background: var(--gold-pale); color: var(--charcoal); font-weight: 500; }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        /* Notification Bell */
        .nav-notif {
            position: relative;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.4rem;
            color: var(--brown);
            transition: color 0.2s;
            display: flex;
        }

        .nav-notif:hover { color: var(--gold); }

        .nav-notif .badge {
            position: absolute;
            top: 0; right: 0;
            width: 16px; height: 16px;
            background: var(--error);
            color: var(--white);
            font-size: 0.6rem;
            font-weight: 600;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            border: 2px solid var(--white);
        }

        /* Profile Dropdown */
        .nav-profile {
            position: relative;
        }

        .profile-trigger {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            background: none;
            border: 1.5px solid var(--cream-dark);
            border-radius: 50px;
            padding: 0.3rem 0.9rem 0.3rem 0.3rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .profile-trigger:hover { border-color: var(--gold-light); }

        .profile-avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%);
            display: flex; align-items: center; justify-content: center;
            color: var(--white);
            font-family: var(--font-display);
            font-size: 0.85rem;
            font-weight: 600;
        }

        .profile-name {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--charcoal);
        }

        .profile-trigger svg {
            width: 14px; height: 14px;
            color: var(--brown-light);
            transition: transform 0.2s;
        }

        .profile-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 220px;
            background: var(--white);
            border: 1px solid var(--cream-dark);
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(44,36,24,0.12);
            padding: 0.5rem;
            display: none;
            z-index: 200;
        }

        .profile-dropdown.show { display: block; }

        .dropdown-header {
            padding: 0.75rem;
            border-bottom: 1px solid var(--cream-dark);
            margin-bottom: 0.25rem;
        }

        .dropdown-header .name {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--charcoal);
        }

        .dropdown-header .email {
            font-size: 0.75rem;
            color: var(--brown-light);
            margin-top: 0.15rem;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.6rem 0.75rem;
            font-size: 0.85rem;
            color: var(--brown);
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.15s;
        }

        .dropdown-item:hover { background: var(--cream); color: var(--charcoal); }
        .dropdown-item svg { width: 16px; height: 16px; flex-shrink: 0; }

        .dropdown-item.logout {
            color: var(--error);
            border-top: 1px solid var(--cream-dark);
            margin-top: 0.25rem;
            padding-top: 0.7rem;
            border-radius: 0 0 6px 6px;
        }

        .dropdown-item.logout:hover { background: #FEF2F2; }

        /* ── MAIN CONTENT ── */
        .main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Welcome Banner */
        .welcome-banner {
            background: linear-gradient(135deg, var(--charcoal) 0%, var(--brown) 60%, var(--gold) 100%);
            border-radius: 16px;
            padding: 2.5rem 3rem;
            position: relative;
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .welcome-banner::before {
            content: '';
            position: absolute;
            top: -50%; right: -20%;
            width: 400px; height: 400px;
            border: 1px solid rgba(184,134,11,0.15);
            border-radius: 50%;
        }

        .welcome-banner::after {
            content: '';
            position: absolute;
            bottom: -60%; right: -10%;
            width: 300px; height: 300px;
            border: 1px solid rgba(184,134,11,0.1);
            border-radius: 50%;
        }

        .welcome-banner h1 {
            font-family: var(--font-display);
            font-size: 2rem;
            font-weight: 400;
            color: var(--gold-pale);
            margin-bottom: 0.4rem;
            position: relative;
            z-index: 1;
        }

        .welcome-banner h1 em { font-weight: 500; color: var(--gold-light); font-style: italic; }

        .welcome-banner p {
            font-size: 0.9rem;
            font-weight: 300;
            color: rgba(255,255,255,0.65);
            position: relative;
            z-index: 1;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--white);
            border: 1px solid var(--cream-dark);
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s;
            cursor: pointer;
        }

        .stat-card:hover {
            border-color: var(--gold-light);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(44,36,24,0.08);
        }

        .stat-card .stat-icon {
            width: 40px; height: 40px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1rem;
        }

        .stat-card .stat-icon svg { width: 20px; height: 20px; }

        .stat-card .stat-icon.gold { background: var(--gold-pale); color: var(--gold); }
        .stat-card .stat-icon.sage { background: #E8EFE5; color: var(--sage); }
        .stat-card .stat-icon.rose { background: #F5EAEA; color: var(--rose); }
        .stat-card .stat-icon.blue { background: #E5EDF5; color: var(--info); }

        .stat-card .stat-value {
            font-family: var(--font-display);
            font-size: 2rem;
            font-weight: 600;
            color: var(--charcoal);
            line-height: 1;
        }

        .stat-card .stat-label {
            font-size: 0.8rem;
            font-weight: 400;
            color: var(--brown-light);
            margin-top: 0.35rem;
            letter-spacing: 0.02em;
        }

        /* Content Grid */
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        @media (max-width: 768px) {
            .content-grid { grid-template-columns: 1fr; }
            .welcome-banner { padding: 1.5rem; }
            .welcome-banner h1 { font-size: 1.5rem; }
            .nav-links { display: none; }
        }

        .card {
            background: var(--white);
            border: 1px solid var(--cream-dark);
            border-radius: 12px;
            overflow: hidden;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--cream-dark);
        }

        .card-header h3 {
            font-family: var(--font-display);
            font-size: 1.15rem;
            font-weight: 600;
            color: var(--charcoal);
        }

        .card-header a {
            font-size: 0.8rem;
            color: var(--gold);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .card-header a:hover { color: var(--charcoal); }

        .card-body { padding: 1.25rem 1.5rem; }

        /* Profile Info Card */
        .profile-info-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 0;
        }

        .profile-info-row + .profile-info-row {
            border-top: 1px solid var(--cream);
        }

        .profile-info-row .label {
            font-size: 0.8rem;
            font-weight: 400;
            color: var(--brown-light);
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .profile-info-row .value {
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--charcoal);
        }

        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.7rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .role-badge.user {
            background: var(--gold-pale);
            color: var(--gold);
        }

        .role-badge.admin {
            background: #E8EFE5;
            color: var(--sage);
        }

        .role-badge .dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        /* Recent Bookings */
        .booking-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.9rem 0;
        }

        .booking-item + .booking-item {
            border-top: 1px solid var(--cream);
        }

        .booking-icon {
            width: 42px; height: 42px;
            border-radius: 10px;
            background: var(--cream);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .booking-icon svg {
            width: 20px; height: 20px;
            color: var(--gold);
        }

        .booking-details { flex: 1; min-width: 0; }

        .booking-details .venue-name {
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--charcoal);
        }

        .booking-details .booking-date {
            font-size: 0.78rem;
            color: var(--brown-light);
            margin-top: 0.15rem;
        }

        .status-badge {
            padding: 0.2rem 0.65rem;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 500;
            letter-spacing: 0.03em;
            flex-shrink: 0;
        }

        .status-badge.confirmed { background: #E8EFE5; color: var(--sage); }
        .status-badge.pending { background: var(--gold-pale); color: var(--gold); }
        .status-badge.cancelled { background: #FEF2F2; color: var(--error); }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }

        .action-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.6rem;
            padding: 1.25rem 0.75rem;
            background: var(--cream);
            border: 1.5px solid transparent;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.25s;
            text-decoration: none;
        }

        .action-btn:hover {
            background: var(--white);
            border-color: var(--gold-light);
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(44,36,24,0.06);
        }

        .action-btn svg { width: 22px; height: 22px; color: var(--gold); }

        .action-btn span {
            font-size: 0.78rem;
            font-weight: 500;
            color: var(--brown);
            text-align: center;
            letter-spacing: 0.02em;
        }

        /* Animations */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .welcome-banner { animation: fadeUp 0.5s ease-out both; }
        .stats-grid { animation: fadeUp 0.5s ease-out 0.1s both; }
        .content-grid { animation: fadeUp 0.5s ease-out 0.2s both; }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="navbar-inner">
            <a href="#" class="nav-brand">
                <div class="nav-brand-icon">
                    <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>
                </div>
                <span class="nav-brand-text">Pendopo <em>Uti</em></span>
            </a>

            <ul class="nav-links">
                <li><a href="#" class="active">Dashboard</a></li>
                <li><a href="#">Venue</a></li>
                <li><a href="#">Booking</a></li>
                <li><a href="#">Survei</a></li>
            </ul>

            <div class="nav-right">
                <button class="nav-notif" title="Notifikasi">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
                    <span class="badge">3</span>
                </button>

                <div class="nav-profile">
                    <button class="profile-trigger" onclick="document.querySelector('.profile-dropdown').classList.toggle('show')">
                        <div class="profile-avatar">Aloy</div>
                        <span class="profile-name">Aloy</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="profile-dropdown">
                        <div class="dropdown-header">
                            <div class="name">Aloy</div>
                            <div class="email">Aloy@example.com</div>
                        </div>
                        <a href="#" class="dropdown-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            Profil Saya
                        </a>
                        <a href="#" class="dropdown-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
                            Pengaturan
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="dropdown-item logout" >
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            @csrf
                            <button type="submit" >Keluar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <main class="main">

        <!-- Welcome Banner -->
        <div class="welcome-banner">
            <h1>Selamat datang, <em>Aloy</em></h1>
            <p>Kelola reservasi dan jadwal pernikahan Anda dari sini.</p>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon gold">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </div>
                <div class="stat-value">2</div>
                <div class="stat-label">Total Booking</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon sage">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div class="stat-value">1</div>
                <div class="stat-label">Dikonfirmasi</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon rose">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div class="stat-value">1</div>
                <div class="stat-label">Menunggu</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </div>
                <div class="stat-value">0</div>
                <div class="stat-label">Jadwal Survei</div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="content-grid">

            <!-- Left: Profile Info -->
            <div class="card">
                <div class="card-header">
                    <h3>Informasi Profil</h3>
                    <a href="#">Edit</a>
                </div>
                <div class="card-body">
                    <div class="profile-info-row">
                        <span class="label">Nama</span>
                        <span class="value">Aloy</span>
                    </div>
                    <div class="profile-info-row">
                        <span class="label">Email</span>
                        <span class="value">refi@example.com</span>
                    </div>
                    <div class="profile-info-row">
                        <span class="label">Telepon</span>
                        <span class="value">081234567890</span>
                    </div>
                    <div class="profile-info-row">
                        <span class="label">Role</span>
                        <span class="value">
                            <span class="role-badge user">
                                <span class="dot"></span>
                                User
                            </span>
                        </span>
                    </div>
                    <div class="profile-info-row">
                        <span class="label">Bergabung</span>
                        <span class="value">6 April 2026</span>
                    </div>
                </div>
            </div>

            <!-- Right: Recent Bookings + Quick Actions -->
            <div style="display:flex; flex-direction:column; gap:1.5rem;">

                <!-- Recent Bookings -->
                <div class="card">
                    <div class="card-header">
                        <h3>Booking Terbaru</h3>
                        <a href="#">Lihat Semua</a>
                    </div>
                    <div class="card-body">
                        <div class="booking-item">
                            <div class="booking-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                            </div>
                            <div class="booking-details">
                                <div class="venue-name">Pendopo Utama</div>
                                <div class="booking-date">BK-20260520 &bull; 20 Mei 2026</div>
                            </div>
                            <span class="status-badge confirmed">Dikonfirmasi</span>
                        </div>
                        <div class="booking-item">
                            <div class="booking-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                            </div>
                            <div class="booking-details">
                                <div class="venue-name">Gedung Serbaguna</div>
                                <div class="booking-date">BK-20260715 &bull; 15 Juli 2026</div>
                            </div>
                            <span class="status-badge pending">Menunggu</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h3>Aksi Cepat</h3>
                    </div>
                    <div class="card-body">
                        <div class="quick-actions">
                            <a href="#" class="action-btn">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="12" y1="14" x2="12" y2="18"/><line x1="10" y1="16" x2="14" y2="16"/></svg>
                                <span>Booking Baru</span>
                            </a>
                            <a href="#" class="action-btn">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <span>Ajukan Survei</span>
                            </a>
                            <a href="#" class="action-btn">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
                                <span>Layanan WO</span>
                            </a>
                            <a href="#" class="action-btn">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                                <span>Hubungi Admin</span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </main>

    <script>
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const dropdown = document.querySelector('.profile-dropdown');
            const trigger = document.querySelector('.profile-trigger');
            if (!trigger.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });

        function handleLogout(e) {
            e.preventDefault();
            if (confirm('Apakah Anda yakin ingin keluar?')) {
                // Nanti di implementasi: call API /api/logout
                // lalu redirect ke login
                window.location.href = 'login.html';
            }
        }
    </script>

</body>
</html>