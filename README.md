# 📌 Tugas Semester 4 & Website POS Laravel

Repository ini berisi kumpulan tugas Semester 4 serta proyek pengembangan website Point of Sales (POS) menggunakan Laravel.

---

## ⚙️ Teknologi yang Digunakan
- PHP (Laravel)
- MySQL
- Python
- Pandas
- Scikit-learn

---

## 🚀 Fitur Website POS
- Manajemen data barang
- Manajemen kategori
- Transaksi penjualan
- Retur penjualan
- Import & export data (Excel/PDF)
- Dashboard admin

---

## 📂 Struktur Repository
- `ML_*.ipynb` → Tugas dan praktikum Machine Learning
- `POS/` → Project website Point of Sales Laravel

---

## 👩‍💻 Tujuan
Repository ini dibuat sebagai media pembelajaran dan dokumentasi hasil pengembangan selama perkuliahan.

---

## 🔧 Instalasi

```bash
# Clone repository
git clone https://github.com/username/nama-repo.git

# Masuk ke folder project
cd nama-repo

# Install dependency
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Konfigurasi database di file .env

# Migrasi database
php artisan migrate

# Jalankan server
php artisan serve
