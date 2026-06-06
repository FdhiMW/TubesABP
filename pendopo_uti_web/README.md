# Pendopo UTI — Web (Laravel)

Backend API dan dashboard admin untuk aplikasi booking wedding venue Pendopo UTI.

---

## Teknologi

- **PHP** >= 8.2
- **Laravel** 12
- **MySQL**
- **Laravel Sanctum** (autentikasi API)
- **Midtrans** (payment gateway)
- **Google Gemini** (AI chatbot)
- **Firebase FCM** (push notification)

---

## Instalasi

### 1. Install dependencies

```bash
composer install
```

### 2. Salin file environment

```bash
cp .env.example .env
```

### 3. Generate application key

```bash
php artisan key:generate
```

### 4. Konfigurasi `.env`

```env
APP_NAME=PendopoUTI
APP_URL=http://127.0.0.1:8000

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

### 5. Buat database

Buat database `pendopo_uti` di MySQL, lalu jalankan migrasi:

```bash
php artisan migrate
```

### 6. (Opsional) Jalankan seeder

```bash
php artisan db:seed
```

### 7. Jalankan server

```bash
php artisan serve
```

Aplikasi berjalan di: `http://127.0.0.1:8000`

---

## Cara Mendapatkan API Key

### Google Gemini
1. Buka [Google AI Studio](https://aistudio.google.com/app/apikey)
2. Klik **Create API Key**
3. Salin key ke `GOOGLE_GEMINI_API_KEY`

### Midtrans
1. Daftar/login di [dashboard.sandbox.midtrans.com](https://dashboard.sandbox.midtrans.com)
2. Pastikan mode **Sandbox** aktif (toggle kiri atas)
3. Buka **Settings → Access Keys**
4. Salin **Server Key** dan **Client Key** ke `.env`

> Key Sandbox diawali `SB-Mid-server-` dan `SB-Mid-client-`.
> Gunakan `MIDTRANS_IS_PRODUCTION=false` selama testing.

### Firebase FCM
1. Buka [Firebase Console](https://console.firebase.google.com)
2. Pilih project → **Project Settings → Service Accounts**
3. Klik **Generate new private key** → download file JSON
4. Simpan file JSON di root project (misal: `firebase-service-account.json`)
5. Isi `FIREBASE_PROJECT_ID` dan `FIREBASE_CREDENTIALS` di `.env`

---

## Struktur Folder Penting

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AdminController.php           # Manajemen booking & survei (admin)
│   │   ├── BookingController.php         # Booking (web)
│   │   ├── PaymentController.php         # Pembayaran Midtrans
│   │   ├── SurveyController.php          # Survei venue
│   │   ├── AiController.php              # AI chatbot
│   │   ├── ManageController.php          # Riwayat & reschedule user
│   │   └── API/                          # Controller khusus Flutter
│   └── Middleware/
│       └── RoleMiddleware.php            # Cek role admin/user
├── Models/
│   ├── Booking.php
│   ├── Survey.php
│   ├── Venue.php
│   ├── Package.php
│   ├── Notification.php
│   └── User.php
└── Services/
    ├── GeminiService.php                 # Integrasi Google Gemini
    ├── FirebaseNotificationService.php   # Integrasi Firebase FCM
    └── AiContextService.php              # Konteks venue untuk AI
```

---

## Role Pengguna

| Role | Akses |
|------|-------|
| `user` | Booking, survei, pembayaran, riwayat |
| `admin` | Semua fitur user + dashboard admin, approve/reject booking & survei, kelola paket |

Untuk membuat akun admin, gunakan Laravel Tinker:

```bash
php artisan tinker
```
```php
App\Models\User::where('email', 'admin@example.com')->update(['role' => 'admin']);
```

---

## API Routes (untuk Flutter)

Semua route API diawali `/api/`. Route yang membutuhkan autentikasi harus menyertakan header:

```
Authorization: Bearer {token}
```

| Method | Endpoint | Keterangan | Auth |
|--------|----------|------------|------|
| POST | `/api/register` | Registrasi | — |
| POST | `/api/login` | Login | — |
| POST | `/api/logout` | Logout | ✓ |
| GET | `/api/me` | Data user login | ✓ |
| GET | `/api/packages` | Daftar paket aktif | ✓ |
| GET | `/api/availability-data` | Data ketersediaan kalender | — |
| POST | `/api/bookings` | Buat booking | ✓ |
| POST | `/api/bookings/{id}/payment` | Buat Snap token pembayaran | ✓ |
| POST | `/api/bookings/callback` | Webhook Midtrans | — |
| POST | `/api/surveys` | Buat survei | ✓ |
| GET | `/api/manage` | Riwayat booking & survei | ✓ |
| POST | `/api/booking/{id}/cancel` | Batalkan booking | ✓ |
| POST | `/api/survey/{id}/cancel` | Batalkan survei | ✓ |
| POST | `/api/booking/{id}/reschedule` | Reschedule booking | ✓ |
| POST | `/api/survey/{id}/reschedule` | Reschedule survei | ✓ |
| POST | `/api/ai/chat` | AI chatbot | ✓ |
| POST | `/api/save-fcm-token` | Simpan token FCM | ✓ |
| GET | `/api/notifications` | Daftar notifikasi | ✓ |

---

## Webhook Midtrans (Lokal)

Midtrans membutuhkan URL publik untuk mengirim notifikasi pembayaran. Saat development di localhost, gunakan [ngrok](https://ngrok.com):

```bash
ngrok http 8000
```

Daftarkan URL yang muncul ke **Midtrans Sandbox → Settings → Configuration → Payment Notification URL**:

```
https://xxxx.ngrok.io/midtrans/callback
```

---

## Perintah Berguna

```bash
# Hapus cache konfigurasi (wajib setelah ubah .env)
php artisan config:clear

# Hapus semua cache
php artisan cache:clear

# Lihat status migrasi
php artisan migrate:status

# Rollback migrasi terakhir
php artisan migrate:rollback
```
