# Pendopo UTI — Aplikasi Booking Wedding Venue

Aplikasi booking wedding venue berbasis web (Laravel) dan mobile (Flutter). Pengguna dapat memesan venue, melakukan survei lokasi, dan membayar via Midtrans. Admin mengelola booking, survei, dan paket melalui dashboard web.

---

## Struktur Project

```
tubesABP/
├── pendopo_uti_web/      # Backend + Web Admin (Laravel 12)
└── pendopo_uti_mobile/   # Aplikasi Mobile (Flutter)
```

---

## Prasyarat

### Web (Laravel)
- PHP >= 8.2
- Composer
- MySQL
- Node.js (untuk asset, opsional)

### Mobile (Flutter)
- Flutter SDK >= 3.11.4
- Android Studio / VS Code
- Emulator atau device Android/iOS

---

## Cara Menjalankan — Web (Laravel)

### 1. Masuk ke folder web

```bash
cd pendopo_uti_web
```

### 2. Install dependencies

```bash
composer install
```

### 3. Salin file environment

```bash
cp .env.example .env
```

### 4. Generate application key

```bash
php artisan key:generate
```

### 5. Konfigurasi `.env`

Buka file `.env` dan sesuaikan:

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pendopo_uti
DB_USERNAME=root
DB_PASSWORD=

# Google Gemini (AI Chatbot)
GOOGLE_GEMINI_API_KEY=your_gemini_api_key
GOOGLE_GEMINI_MODEL=gemini-2.5-flash

# Midtrans (Payment Gateway)
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxx
MIDTRANS_IS_PRODUCTION=false

# Firebase (Push Notification)
FIREBASE_PROJECT_ID=your_project_id
FIREBASE_CREDENTIALS=firebase-service-account.json
```

**Cara mendapatkan key:**
- **Gemini**: [Google AI Studio](https://aistudio.google.com/app/apikey)
- **Midtrans**: [Dashboard Midtrans Sandbox](https://dashboard.sandbox.midtrans.com) → Settings → Access Keys
- **Firebase**: [Firebase Console](https://console.firebase.google.com) → Project Settings → Service Accounts → Generate new private key → simpan file JSON di root project

### 6. Buat database

Buat database `pendopo_uti` di MySQL, lalu jalankan migrasi:

```bash
php artisan migrate
```

### 7. (Opsional) Jalankan seeder

```bash
php artisan db:seed
```

### 8. Jalankan server

```bash
php artisan serve
```

Aplikasi berjalan di: `http://127.0.0.1:8000`

---

## Cara Menjalankan — Mobile (Flutter)

### 1. Masuk ke folder mobile

```bash
cd pendopo_uti_mobile
```

### 2. Install dependencies

```bash
flutter pub get
```

### 3. Sesuaikan base URL API

Buka `lib/services/auth_service.dart` dan ubah `baseUrl` sesuai IP komputer kamu:

```dart
static const String baseUrl = 'http://192.168.1.xxx:8000/api';
```

> Gunakan IP lokal komputer (bukan `localhost`), karena emulator tidak bisa mengakses `localhost` komputer host.
> Cek IP lokal dengan `ipconfig` (Windows) atau `ifconfig` (Mac/Linux).

### 4. Jalankan aplikasi

```bash
flutter run
```

---

## Akun Default

Buat akun admin langsung dari database atau via seeder. Pastikan kolom `role` diisi `admin` pada tabel `users`.

---

## Fitur

| Fitur | Web | Mobile |
|---|---|---|
| Register & Login | ✓ | ✓ |
| Booking Venue | ✓ | ✓ |
| Survei Venue | ✓ | ✓ |
| Pembayaran (Midtrans Snap) | ✓ | ✓ |
| AI Chatbot (Google Gemini) | ✓ | ✓ |
| Push Notification (Firebase FCM) | — | ✓ |
| Dashboard Admin | ✓ | — |
| Manajemen Paket | ✓ | — |

---

## Catatan

- Gunakan key **Sandbox** Midtrans untuk testing (bukan Production).
- Webhook callback Midtrans membutuhkan URL publik. Gunakan [ngrok](https://ngrok.com) saat development: `ngrok http 8000`, lalu daftarkan URL-nya di Midtrans → Settings → Configuration → Payment Notification URL.
- Firebase push notification hanya aktif jika `FIREBASE_CREDENTIALS` sudah dikonfigurasi dengan benar.
