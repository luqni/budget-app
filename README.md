<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="250" alt="Laravel Logo">
</p>

<h1 align="center">💰 Qanaah - Aplikasi Budgeting & Refleksi Keuangan</h1>

<p align="center">
  <b>Aplikasi open source untuk mencatat, memantau, dan mengelola keuangan keluarga dengan sentuhan nilai-nilai Islami.</b>
</p>

<p align="center">
  <a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Laravel Version"></a>
  <a href="https://opensource.org/licenses/MIT"><img src="https://img.shields.io/badge/license-MIT-green" alt="License"></a>
</p>

---

## ✨ Fitur Utama

### 📊 Manajemen Keuangan
- **Pencatatan Pemasukan & Pengeluaran**: Catat transaksi harian dengan mudah dan cepat.
- **Kategori Pengeluaran**: Kelompokkan pengeluaran berdasarkan kategori untuk analisis yang lebih baik.
- **Pengeluaran Berulang (Recurring Expenses)**: Fitur otomatis untuk menyalin pengeluaran rutin ke bulan berikutnya.
- **Dashboard Interaktif**: Grafik tren bulanan dan breakdown pengeluaran per kategori.
- **Filter Periode**: Lihat riwayat keuangan berdasarkan bulan dan tahun.

### 🕌 Fitur Islami & Refleksi
- **Daily Finance Wisdom**: Tampilkan kutipan ayat Al-Qur'an, Hadis, atau kata mutiara tentang muamalah dan keuangan setiap hari.
- **Tadabbur & Catatan**: Fitur untuk mencatat refleksi keuangan harian atau catatan penting lainnya.
- **Notifikasi Harian**: Pengingat harian untuk melihat wisdom keuangan terbaru.

### 🚀 Fitur Teknis & Lainnya
- **PWA Support**: Aplikasi dapat diinstal di perangkat mobile layaknya aplikasi native.
- **Tracking Statistik Aplikasi**: Melacak jumlah download/install aplikasi.
- **Mode Tamu & User**: Halaman publik untuk melihat fitur aplikasi.
- **Dukungan Docker**: Siap dideploy dengan mudah menggunakan Docker (support Easypanel).
- **Database Ringan**: Menggunakan SQLite secara default, mudah dikonfigurasi.

---

## 📸 Tampilan Aplikasi

### 🏠 Dashboard Utama
![Dashboard](https://github.com/luqni/budget-app/blob/main/screenshot/Screen%20Shot%202026-01-31%20at%2016.23.24.png)

### 📊 Statistik Pengeluaran
![Grafik Bulanan Pengeluaran](https://github.com/luqni/budget-app/blob/main/screenshot/Screen%20Shot%202026-01-31%20at%2016.23.54.png)

### 📝 Pencatatan Transaksi
![Form Pengeluaran](https://github.com/luqni/budget-app/blob/main/screenshot/Screen%20Shot%202026-01-31%20at%2016.24.22.png)

---

## ⚙️ Teknologi yang Digunakan

-   **Laravel 11**
-   **PHP 8.2+**
-   **SQLite Database**
-   **Tailwind CSS / Bootstrap 5**
-   **Docker & Docker Compose**
-   **Blade Template Engine**

---

## 🚀 Instalasi

### Opsi 1: Instalasi Manual

1. **Clone Repository**
   ```bash
   git clone https://github.com/luqni/budget-app.git
   cd budget-app
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install && npm run build
   ```

3. **Konfigurasi Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Atur koneksi database di `.env` (default SQLite):
   ```env
   DB_CONNECTION=sqlite
   ```
   Buat file database jika belum ada:
   ```bash
   touch database/database.sqlite
   ```

4. **Jalankan Migrasi**
   ```bash
   php artisan migrate
   ```

5. **Jalankan Aplikasi**
   ```bash
   php artisan serve
   ```

### Opsi 2: Menggunakan Docker

1. **Build & Run Container**
   ```bash
   docker-compose up -d --build
   ```
   
2. **Setup Awal** (Jalankan sekali saja)
   ```bash
   docker-compose exec app php artisan key:generate
   docker-compose exec app php artisan migrate
   ```

---

## 🔒 Lisensi

Proyek ini menggunakan lisensi [MIT License](https://opensource.org/licenses/MIT).

## 💬 Dukungan

Jika kamu menyukai proyek ini, bantu dengan ⭐️ memberi star di GitHub.
Atau jika ingin berdiskusi, buka Issues untuk memberikan ide atau melaporkan bug.

<p align="center">Dibuat dengan ❤️ oleh <b>Muhammad Luqni Baehaqi</b></p>
