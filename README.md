# Global Supply Chain Risk Intelligence Platform

Platform Intelijen Risiko Rantai Pasok Global (Global Supply Chain Risk Intelligence) adalah aplikasi berbasis web yang dirancang untuk memantau, menganalisis, dan memprediksi risiko logistik di berbagai negara secara real-time. Aplikasi ini membantu manajer rantai pasok mengidentifikasi potensi gangguan logistik sebelum terjadi dengan mengintegrasikan data cuaca, indikator makroekonomi, fluktuasi mata uang, serta sentimen berita global.

---

## 🚀 Fitur Utama

1. **Autentikasi & Multi-role (Bootstrap 5)**:
   - Sistem login kustom terproteksi untuk **Administrator** dan **Regular User** dengan visual antarmuka modern (dark mode & glassmorphism).
2. **Dashboard Overview**:
   - Widget metrik utama (negara terpantau, pelabuhan logistik, jumlah status berisiko tinggi).
   - Tabel peringkat risiko negara terpantau lengkap dengan persentase gauge visual.
3. **Peta Risiko Cuaca Interaktif (Weather Map)**:
   - Visualisasi berbasis peta Leaflet.js dengan penanda (marker) interaktif yang berubah warna berdasarkan tingkat risiko negara.
   - Mengambil data suhu, kecepatan angin, curah hujan secara live via **Open-Meteo API**.
4. **Peta Pelabuhan Logistik & Cuaca Live (Port Map)**:
   - Menampilkan koordinat pelabuhan utama dunia di peta.
   - Integrasi langsung dengan API cuaca untuk memantau kecepatan angin dan badai guntur lokal pelabuhan yang bisa mengganggu aktivitas kapal kontainer.
   - Filter cepat berdasarkan negara terpilih.
5. **Detail Asesmen Risiko Negara (Country Assessment)**:
   - Profil lengkap negara (ibu kota, populasi, mata uang, subregion).
   - Metrik makroekonomi live (GDP Nominal, inflasi tahunan, kontribusi ekspor) yang terintegrasi langsung dengan **World Bank API**.
   - Grafik garis interaktif 7 hari fluktuasi mata uang terhadap USD menggunakan **Chart.js** (berbasis **ExchangeRate API**).
   - Feed berita logistik dengan visualisasi warna sentimen dari hasil analisis teks berita **GNews API**.
6. **Analisis Sentimen Lexicon-based**:
   - Sistem membaca judul dan deskripsi artikel secara otomatis dan mencocokkan kata dengan basis data kata positif (`positive_words`) dan kata negatif (`negative_words`) di database.
   - Menghitung sentimen artikel secara otomatis (Positif, Negatif, Netral) sebagai salah satu pilar penentu risiko.
7. **Daftar Pantauan Kustom (Watchlist)**:
   - Pengguna dapat menambahkan atau menghapus negara terpilih ke daftar pantauan mereka secara dinamis menggunakan AJAX.
8. **Panel Kontrol Admin (Admin Panel)**:
   - CRUD kata positif/negatif untuk memperkaya kamus sentimen (lexicon) dan memicu pembaruan analisis berita secara otomatis.
   - Daftar akun pengguna sistem.

---

## 🛠️ Stack Teknologi

- **Backend**: Laravel 13, PHP 8.3+
- **Frontend**: HTML5, Vanilla CSS3 (Custom Theme: Dark Space Premium), Bootstrap 5.3
- **Database**: MySQL (untuk relasi data pengguna, pelabuhan, negara, kamus lexicon, log skor, dan cache berita)
- **Library Visualisasi**:
  - [Leaflet.js](https://leafletjs.com/) (Peta Geospatial)
  - [Chart.js](https://www.chartjs.org/) (Grafik Keuangan & Perbandingan Pilar)
- **API Eksternal Terintegrasi**:
  - **Open-Meteo API** (Data Cuaca Tanpa Kunci API)
  - **World Bank API** (Data Makroekonomi Tanpa Kunci API)
  - **ExchangeRate API** (Nilai Tukar Mata Uang Dunia)
  - **GNews API** (Umpan Berita dengan Mock Fallback jika kunci tidak ada)

---

## ⚙️ Formula Perhitungan Risiko

Sistem menggunakan algoritma pembobotan tertimbang (weighted scoring) dari 4 pilar indikator:
$$\text{Total Skor Risiko} = (\text{Skor Cuaca} \times 0.3) + (\text{Skor Ekonomi} \times 0.2) + (\text{Skor Sentimen Berita} \times 0.4) + (\text{Skor Fluktuasi Valuta} \times 0.1)$$

- **Pilar Cuaca (30%)**: Berdasarkan indikator cuaca ekstrim (Badai guntur/Thunderstorm = 100, Hujan lebat/Rainy = 50, Suhu ekstrim = 30).
- **Pilar Ekonomi (20%)**: Berdasarkan tingkat inflasi (Inflasi > 10% = 100, > 5% = 60, > 3% = 30, stabil = 0).
- **Pilar Berita/Sentimen (40%)**: Berdasarkan persentase artikel bernada negatif yang terdeteksi oleh kamus lexicon terhadap total artikel baru.
- **Pilar Valuta (10%)**: Dihitung dari volatilitas nilai tukar mata uang lokal terhadap USD dalam 7 hari terakhir.

---

## 💻 Panduan Instalasi & Menjalankan

1. **Clone repository**:
   ```bash
   git clone https://github.com/zhraanjani06/Project-Final.git
   cd Project-Final
   ```

2. **Install dependensi PHP**:
   ```bash
   composer install
   ```

3. **Salin file konfigurasi lingkungan**:
   ```bash
   cp .env.example .env
   ```

4. **Konfigurasi Database MySQL di `.env`**:
   Buka file `.env` dan sesuaikan nama database, user, dan password Anda:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=project_final
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Generate key Laravel**:
   ```bash
   php artisan key:generate
   ```

6. **Migrasi database dan jalankan Seeder**:
   Perintah ini akan membuat semua tabel database dan mengisi data bawaan pengguna (Admin & User), daftar pelabuhan utama, koordinat negara terpantau, serta kamus dasar kata lexicon.
   ```bash
   php artisan migrate:fresh --seed
   ```

7. **Jalankan Aplikasi**:
   ```bash
   php artisan serve
   ```
   Buka peramban (browser) dan akses alamat: `http://127.0.0.1:8000`

---

## 🔐 Kredensial Login Pengujian

- **Akun Administrator**:
  - Email: `admin@example.com`
  - Password: `password`
  - Peran: Admin (Memiliki akses penuh ke Admin Panel Lexicon)
- **Akun Pengguna Biasa**:
  - Email: `user@example.com`
  - Password: `password`
  - Peran: User
