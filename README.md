# Pendopo UTI — Wedding Venue Booking System

Aplikasi pemesanan venue pernikahan berbasis **Laravel 12** (web & REST API) dan **Flutter** (mobile Android). Proyek ini dikembangkan sebagai tugas akhir mata kuliah Aplikasi Berbasis Platform (ABP).

---

## Struktur Proyek

```
tubesABP/
├── pendopo_uti_web/      # Backend Laravel 12 + Web Admin
└── pendopo_uti_mobile/   # Aplikasi Mobile Flutter
```

---

## Tech Stack

| Layer      | Teknologi                         |
| ---------- | --------------------------------- |
| Backend    | Laravel 12, PHP ^8.2, MySQL       |
| Auth API   | Laravel Sanctum                   |
| Payment    | Midtrans Snap (sandbox)           |
| Push Notif | Firebase FCM (Cloud Messaging v1) |
| AI Chatbot | Google Gemini API                 |
| Mobile     | Flutter ^3.11.4, Dart             |
| Maps       | flutter_map + OpenStreetMap       |

---

## Fitur

| Fitur                                 | Web | Mobile |
| ------------------------------------- | --- | ------ |
| Register & Login                      | ✓   | ✓      |
| Booking Venue                         | ✓   | ✓      |
| Booking Survey Kunjungan              | ✓   | ✓      |
| Lihat Ketersediaan Tanggal            | ✓   | ✓      |
| Pembayaran via Midtrans Snap          | ✓   | ✓      |
| Kelola Booking (batalkan, reschedule) | ✓   | ✓      |
| Peta Lokasi Venue                     | —   | ✓      |
| AI Chatbot (Google Gemini)            | ✓   | ✓      |
| Push Notification (FCM)               | —   | ✓      |
| Dashboard Admin                       | ✓   | —      |
| Manajemen Venue & Paket               | ✓   | —      |

---

## Aturan Booking

- Jam operasional venue: **07:00 – 22:00**
- Maksimal **2 booking per tanggal** (dihitung gabungan booking + survey aktif)
- Booking seharian (07:00–22:00) diblokir jika sudah ada slot lain di tanggal tersebut
- Waktu tidak boleh bentrok dengan booking atau survey yang sudah ada
- Jumlah tamu tidak boleh melebihi kapasitas venue

---

## Cara Menjalankan

### Prasyarat

- PHP 8.2+, Composer
- MySQL
- Flutter SDK ^3.11.4
- Laragon (atau server lokal lain)
- Android device / emulator

---

### 1. Backend (Laravel)

```bash
cd pendopo_uti_web

# Install dependensi
composer install

# Salin env
cp .env.example .env

# Generate key
php artisan key:generate

# Buat database MySQL bernama pendopo_uti, lalu jalankan migrasi
php artisan migrate --seed

# Jalankan server
php artisan serve
```

Konfigurasi wajib di `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pendopo_uti
DB_USERNAME=root
DB_PASSWORD=

MIDTRANS_SERVER_KEY=Mid-server-xxxx
MIDTRANS_CLIENT_KEY=Mid-client-xxxx
MIDTRANS_IS_PRODUCTION=false

FIREBASE_PROJECT_ID=your-firebase-project-id
FIREBASE_CREDENTIALS=storage/app/firebase/your-service-account.json

GOOGLE_GEMINI_API_KEY=your-gemini-api-key
GOOGLE_GEMINI_MODEL=gemini-2.5-flash
```

Cara mendapatkan key:

- **Midtrans**: [dashboard.sandbox.midtrans.com](https://dashboard.sandbox.midtrans.com) → Settings → Access Keys
- **Firebase**: [console.firebase.google.com](https://console.firebase.google.com) → Project Settings → Service Accounts → Generate new private key → simpan JSON di `storage/app/firebase/`
- **Gemini**: [aistudio.google.com/app/apikey](https://aistudio.google.com/app/apikey)

---

### 2. Mobile (Flutter)

```bash
cd pendopo_uti_mobile

# Install dependensi
flutter pub get
```

**Konfigurasi IP server** — edit `lib/services/auth_service.dart`:

```dart
// Ganti dengan IP laptop yang menjalankan Laravel
// Pastikan device & laptop terhubung ke WiFi yang sama
// Cek IP: Windows → ipconfig | macOS/Linux → ifconfig
static const String baseUrl = 'http://192.168.X.X:8000/api';
```

**Firebase (FCM)** — letakkan file `google-services.json` di `android/app/`:

```
pendopo_uti_mobile/android/app/google-services.json
```

> File ini tidak di-commit ke repo (sudah ada di `.gitignore`).
> Download dari Firebase Console → Project Settings → Android App.

```bash
# Jalankan aplikasi
flutter run
```

---

## Struktur Database

| Tabel                    | Keterangan                            |
| ------------------------ | ------------------------------------- |
| `users`                  | Data pengguna & FCM token device      |
| `venues`                 | Data venue (nama, kapasitas, lokasi)  |
| `packages`               | Paket pernikahan (nama, harga, fitur) |
| `bookings`               | Data pemesanan venue                  |
| `surveys`                | Data booking survey kunjungan lokasi  |
| `notifications`          | Notifikasi per user                   |
| `personal_access_tokens` | Token Sanctum untuk auth API          |

---

## API Endpoints

### Public

| Method | Endpoint                 | Keterangan                   |
| ------ | ------------------------ | ---------------------------- |
| POST   | `/api/register`          | Registrasi akun baru         |
| POST   | `/api/login`             | Login, mendapat Bearer token |
| GET    | `/api/availability-data` | Data ketersediaan kalender   |

### Protected (Bearer Token)

| Method | Endpoint                             | Keterangan                     |
| ------ | ------------------------------------ | ------------------------------ |
| GET    | `/api/venues`                        | Daftar venue beserta kapasitas |
| GET    | `/api/packages`                      | Daftar paket aktif             |
| POST   | `/api/bookings`                      | Buat booking venue baru        |
| POST   | `/api/surveys`                       | Buat booking survey            |
| GET    | `/api/manage`                        | Booking & survey milik user    |
| POST   | `/api/bookings/{id}/payment`         | Generate Midtrans Snap token   |
| POST   | `/api/bookings/{id}/confirm-payment` | Cek & update status pembayaran |
| POST   | `/api/booking/{id}/cancel`           | Batalkan booking               |
| POST   | `/api/booking/{id}/reschedule`       | Reschedule booking             |
| POST   | `/api/survey/{id}/cancel`            | Batalkan survey                |
| POST   | `/api/survey/{id}/reschedule`        | Reschedule survey              |
| POST   | `/api/save-fcm-token`                | Simpan FCM token device        |
| GET    | `/api/notifications`                 | Daftar notifikasi user         |
| POST   | `/api/ai/chat`                       | AI Chatbot (Google Gemini)     |

---

## Catatan Firebase / FCM

- Gunakan **satu project Firebase** yang sama untuk backend dan mobile
- Service account JSON wajib ada di `pendopo_uti_web/storage/app/firebase/`
- `google-services.json` wajib ada di `pendopo_uti_mobile/android/app/` — **jangan di-commit ke git**
- FCM menggunakan **HTTP v1 API** dengan OAuth2 service account credentials
- Jika `google-services.json` sudah terlanjur ter-commit, hapus dari tracking:

```bash
git rm --cached pendopo_uti_mobile/android/app/google-services.json
git commit -m "chore: remove google-services.json from tracking"
```

---

## Catatan Midtrans

- Gunakan key **Sandbox** untuk testing (bukan Production)
- Webhook callback Midtrans membutuhkan URL publik. Gunakan [ngrok](https://ngrok.com) saat development:

```bash
ngrok http 8000
```

Daftarkan URL ngrok di Midtrans → Settings → Configuration → Payment Notification URL:

```
https://xxxx.ngrok.io/api/bookings/callback
```

---

## Lisensi

Proyek ini dibuat untuk keperluan akademik.
