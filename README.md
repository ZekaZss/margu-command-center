# ⚓ MARGU - Advanced Command Center (V1.0)

[![Security Protocol](https://img.shields.io/badge/Security-AES--256-brightgreen)](https://github.com/ZekaZss/margu-command-center)
[![Framework](https://img.shields.io/badge/Backend-Laravel%2011-red)](https://laravel.com)
[![Engine](https://img.shields.io/badge/Desktop-Tauri%20%2B%20Rust-orange)](https://tauri.app)

MARGU Command Center adalah perangkat lunak taktis berbasis desktop yang dirancang khusus untuk memantau pergerakan kapal nelayan dan unit patroli secara *real-time*. Sistem ini mengintegrasikan pelacakan satelit dengan enkripsi keamanan berlapis guna mendeteksi ancaman di perairan secara instan.

---

## 📥 LAUNCHER UNDUHAN RESMI (WINDOWS)

Silakan klik tautan di bawah ini untuk mengunduh berkas instalasi mandiri untuk sistem operasi Windows:

### 🚀 [KLIK DI SINI UNTUK DOWNLOAD MARGU INSTALLER (.EXE)](PASTE_LINK_DOWNLOAD_YANG_KAMU_COPY_DISINI)

*(Catatan: Setelah mengunduh, cukup klik dua kali berkas `Margu_0.1.0_x64-setup.exe` untuk memulai proses instalasi otomatis di komputer Anda).*

---

## 🔥 Fitur Unggulan Sistem

### 1. 🛰️ Live Radar Tracking & History Trace
Memetakan koordinat garis lintang dan bujur secara *real-time* menggunakan Leaflet peta gelap interaktif. Dilengkapi garis putus-putus (*Trace History*) untuk melihat jalur pelayaran yang telah dilewati objek.

### 2. 🚨 Critical SOS Emergency Signal
Apabila perangkat keras (*hardware*) di laut mengirimkan sinyal darurat, pangkalan pusat akan langsung membunyikan sirine bahaya otomatis, mengubah warna panel menjadi merah berkedip, dan melakukan *auto-focus* kamera radar ke lokasi kecelakaan.

### 3. 🔐 API Gatekeeper Security
Gerbang masuk data dari luar dilindungi penuh oleh token rahasia militer (`MARGU-SECURE-KEY-2026`). Setiap tembakan data dari perangkat tak dikenal yang tidak membawa token ini akan langsung diblokir otomatis oleh *firewall server*.

### 4. 📋 Split-Directory Dashboard
Manajemen antarmuka pintar dua kolom terpisah:
* **Kolom Kiri:** Memantau pergerakan unit patroli militer resmi (KRI TNI AL).
* **Kolom Kanan:** Memantau aktivitas kapal sipil dan mendeteksi adanya sinyal pemancar asing ilegal (*Undeclared/Alien Target*).

---

## 🛠️ Arsitektur Teknologi

Sistem dikembangkan dengan kombinasi performa tinggi dan keamanan tingkat rendah (*low-level*):
* **Core Engine:** Rust (Tauri Desktop Environment) - Menjamin aplikasi super ringan dan kebal injeksi memori.
* **Brain Center:** PHP 8.x + Laravel 11 - Mengelola database, lalu lintas API, dan enkripsi token.
* **Database Engine:** MySQL - Menyimpan data log koordinat dan riwayat kapal.
* **Frontend HUD:** Bootstrap 5 + Leaflet.js (Aesthetic Cyberpunk Dark Mode).

---

## ⚙️ Petunjuk Pengoperasian Server Lokal

Jika Anda ingin menjalankan pangkalan pusat dari kode mentah, ikuti urutan perintah berikut:

1. **Aktifkan Server Utama & Database:**
   ```bash
   php artisan serve
