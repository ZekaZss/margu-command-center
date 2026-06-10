⚓ MARGU - Tactical Command Center
Advanced IoT Radar Tracking & Command System

📌 Tentang Proyek
MARGU Command Center adalah sebuah perangkat lunak sistem pelacakan radar real-time berbasis desktop (Windows) yang dirancang khusus untuk keperluan komando taktis dan keselamatan maritim.

Sistem ini berfungsi sebagai "Layar Monitor Utama" bagi pihak otoritas (seperti TNI AL atau Tim SAR) untuk memantau, melacak, dan merespons perangkat jam tangan pintar (IoT) yang digunakan oleh para nelayan saat melaut. MARGU menjembatani perangkat keras IoT di lapangan dengan pangkalan pusat melalui antarmuka radar yang sangat responsif, informatif, dan mengedepankan efisiensi operasional militer.

Teknologi yang Digunakan (Tech Stack):

Frontend: HTML5, CSS3, Vanilla JavaScript, Bootstrap 5, Leaflet.js (Radar Mapping).

Backend: Laravel 11 (PHP), MySQL Database, RESTful API.

Desktop Wrapper: Tauri Framework, Rust (Mengubah sistem web menjadi aplikasi .exe mandiri).

✨ Fitur Utama
Live GPS Tracking & Trace History: Memantau pergerakan kapal nelayan dan unit patroli secara real-time beserta garis jejak pergerakan terakhirnya.

Critical SOS Emergency Alert: Sistem akan membunyikan alarm otomatis dan kamera radar akan mengunci lokasi nelayan yang menekan tombol SOS darurat di jam tangannya.

Undeclared Device Detection: Secara otomatis mendeteksi dan menandai sinyal GPS dari perangkat asing/ilegal yang tidak terdaftar di sistem.

Hardware Health Monitoring: Menampilkan status sisa baterai (🔋) dan kekuatan sinyal (📶) dari jam tangan IoT nelayan langsung dari layar radar.

Split-Directory Dashboard: Manajemen aset dua kolom yang memisahkan data armada militer (KRI) dengan armada sipil untuk keterbacaan tingkat tinggi.

⚙️ Cara Menjalankan Sistem (Setup)
Karena MARGU adalah aplikasi terpadu, sistem ini membutuhkan backend untuk menyala sebelum aplikasi radarnya dibuka.

Pastikan Laragon (atau server lokal sejenis) sudah berjalan (Apache & MySQL aktif).

Buka terminal di dalam folder proyek ini.

Bersihkan database untuk simulasi baru (opsional): php artisan migrate:fresh

Nyalakan mesin backend API: php artisan serve

Buka terminal baru, dan luncurkan aplikasi desktop MARGU: npx tauri dev (Aplikasi akan terbuka otomatis).

📖 Panduan Penggunaan Langkah demi Langkah
Langkah 1: Registrasi Perangkat (Bind Device)
Sebelum alat digunakan di laut, petugas harus mendaftarkannya terlebih dahulu.

Klik menu ⚙️ BIND DEVICE di pojok kiri bawah.

Masukkan Kode Perangkat / Serial Number (contoh: MARGU-NEL-001).

Tentukan lokasi koordinat awal pembagian alat (Latitude & Longitude).

Pilih status awal alat (OFFLINE, SECURE, atau PATROL).

Klik tombol BIND DEVICE untuk menyimpan ke database. Titik akan langsung muncul di radar.

Langkah 2: Pemantauan Radar (Live Tracking)

Saat perangkat IoT mulai mengirimkan koordinat baru ke database, titik di radar MARGU akan bergerak secara otomatis.

Arahkan kursor (Hover) ke titik mana pun di peta untuk memunculkan kotak informasi holografik berisi detail perangkat, status alat, sisa baterai, dan sinyal.

Jika titik bergerak, radar akan menggambar garis putus-putus di belakangnya sebagai jejak sejarah pelayaran (Movement Traces).

Langkah 3: Simulasi Sistem Darurat (SOS & Alien Signal)

Sinyal SOS: Jika sebuah alat mengubah statusnya menjadi "SOS", radar akan membunyikan sirine dan indikator merah akan berkedip. Klik tombol 🚨 Emergency Alerts di menu kiri, maka kamera akan langsung melakukan zoom-in secara otomatis ke lokasi korban tanpa harus dicari manual.

Perangkat Asing: Untuk menguji respons keamanan, klik tombol ⚠️ TEST ALIEN SIGNAL. Sistem akan memunculkan titik berwarna kuning (UNKNOWN) yang menandakan ada sinyal jam yang masuk ke radar tapi nomor serinya ilegal/tidak terdaftar.

Langkah 4: Manajemen Direktori & Alarm

Jika alarm SOS berbunyi, operator dapat mematikannya (Mute) dengan mengklik tombol 🔊 ALARM SYSTEM: ARMED di pojok kiri bawah.

Klik menu 📋 Device Directory untuk membuka panel layar penuh. Operator dapat melihat daftar lengkap semua kapal militer dan nelayan sipil beserta kondisi baterai dan statusnya yang diperbarui setiap 3 detik.
