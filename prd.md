# PRODUCT REQUIREMENTS & DATABASE SCHEMA (PRD)
**Project:** Platform Ticketing Nobar Piala Dunia - Solo Paragon
**Tech Stack:** Next.js 14 (Vercel) & Laravel 12 (Railway - PostgreSQL)
**Workflow:** VSCode -> GitHub -> Auto-Deploy

## 1. Arsitektur & Infrastruktur
* **Frontend (Public & Admin):** Next.js 14 (App Router). Dideploy ke Vercel.
* **Backend (REST API):** Laravel 12. Dideploy di Railway (PostgreSQL).
* **Zona Waktu:** Seluruh server, database, dan framework WAJIB dikunci ke `Asia/Jakarta` (WIB).

## 2. Concurrency & Anti Race Condition (Kritikal)
* **Pessimistic Locking:** Menggunakan `lockForUpdate()` di Laravel. Saat user checkout, 1 tiket dikunci 15 menit (status `RESERVED`).
* **Auto-Release Worker:** Laravel Queue dieksekusi menit ke-15. Jika Midtrans gagal bayar, ubah tiket jadi `CANCELED` dan kembalikan stok.
* **Anti N+1:** Wajib Eager Loading (`with()`) dan aktifkan `Model::preventLazyLoading()`.

## 3. UI/UX & Keamanan
* **Tema:** Sporty, bebas *AI slop*. Aset resmi Piala Dunia.
* **Warna:** Average Green (#3CAC3B), Torch Red (#E61D25), Hermes (#2A398D), Light Gray (#D1D4D1), Dark Heather Grey (#474A4A).
* **Keamanan:** FormRequest strict validation, Rate Limiting ketat (API, OTP, Checkout), CORS strict ke domain vercel.

## 4. Flow Integrasi
* **Tanpa Login:** Form (Nama, Umur, IG, WA, Email) -> Verifikasi OTP via Resend Email -> Cek & Kunci Stok -> Bayar.
* **Payment (Midtrans Core API):** Headless. Channel: VA (Mandiri, BNI, BRI), Gopay, QRIS.
* **E-Ticket:** QR Code berbasis UUIDv4 (stateless). Dikirim via Resend Email.

## 5. Dashboard Admin & Gate Management
* **Akses:** Single Admin (`admin@nobar.com`). Setup via Tinker, tanpa fitur register.
* **Fitur:** CRUD Match (API Bendera, Hot Match flag), Data Transaksi, Export Excel.
* **Scanner:** Generate Unique Scanner Link untuk gatekeeper (bisa di-revoke).
* **Pass-Out System:** Scan valid (CHECKED_IN). Jika double scan, ada opsi "OK (Checkout)" agar bisa masuk kembali.

---

## 6. Entity Relationship Diagram (ERD) / Skema Database

### `users` (Super Admin)
* id (bigint, PK), name, email (UNIQUE), password, remember_token, timestamps.

### `matches` (Target Lock - Race Condition)
* id (bigint, PK)
* team_a, team_b, flag_a_url, flag_b_url
* match_date (timestamp) -> *Indexed*
* venue (default 'Solo Paragon'), price (integer)
* quota (integer) -> **Master Stok**
* is_hot_match (boolean), timestamps

### `otps` (Verifikasi Email Stateless)
* id (bigint, PK)
* email (varchar) -> *Indexed*
* otp_code (varchar)
* expires_at (timestamp), is_used (boolean), timestamps

### `transactions` (Denormalisasi Data Pembeli)
* id (uuid, PK) -> **Midtrans Order ID**
* match_id (bigint, FK) -> *Indexed*
* buyer_name, buyer_email (*Indexed*), buyer_whatsapp, buyer_instagram, buyer_age
* qty (integer), total_amount (integer)
* payment_method, midtrans_snap_token, payment_url_or_va
* payment_status (enum: PENDING, PAID, EXPIRED, CANCELED) -> *Indexed*
* locked_until (timestamp) -> **Batas 15 menit**
* timestamps

### `tickets` (E-Ticket / QR Payload)
* id (uuid, PK) -> **Payload QR Code**
* transaction_id (uuid, FK) -> *Indexed*
* match_id (bigint, FK)
* status (enum: RESERVED, VALID, CHECKED_IN, CANCELED) -> *Indexed*
* scanned_at (timestamp), timestamps

### `app_settings`
* key (varchar, PK) -> *Contoh: 'scanner_auth_token'*
* value (text), timestamps