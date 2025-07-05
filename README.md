# Toko - Sistem Manajemen Penjualan

## Info Versi

- PHP: >= 8.2
- CodeIgniter: 4.x
- Bootstrap: 5.x

Sistem ini adalah aplikasi web toko sederhana berbasis CodeIgniter 4 yang mendukung manajemen produk, kategori, diskon, transaksi, keranjang belanja, checkout, serta dashboard monitoring penjualan.

## Fitur

- **Manajemen Produk**: CRUD produk, upload foto, stok, harga, dan kategori.
- **Manajemen Kategori Produk**: Tambah, edit, hapus kategori produk.
- **Manajemen Diskon**: CRUD diskon harian, diskon otomatis diterapkan pada checkout dan keranjang jika ada diskon aktif.
- **Keranjang Belanja**: Tambah produk ke keranjang, edit jumlah, hapus item, kosongkan keranjang.
- **Checkout**: Proses checkout dengan perhitungan subtotal, diskon, ongkir, dan total bayar.
- **Transaksi**: Penyimpanan transaksi dan detail pembelian, riwayat transaksi per user.
- **Dashboard Toko**: Monitoring transaksi, total harga, ongkir, status, tanggal, dan jumlah item per transaksi.
- **API Internal**: Endpoint API untuk mengambil data transaksi beserta detailnya (dengan API key).
- **Autentikasi**: Login user/admin, proteksi halaman tertentu.
- **Validasi & Notifikasi**: Validasi form, notifikasi sukses/gagal, error handling AJAX.
- **Tampilan Responsive**: Menggunakan Bootstrap 5 dan template NiceAdmin.
- **DataTables**: Tabel dinamis dengan search, pagination, dan entries per page.

## Instalasi

1. **Clone repository**
   ```bash
   git clone <repo-url> && cd projekCI2
   ```
2. **Install dependency PHP**
   ```bash
   composer install
   ```
3. **Konfigurasi Database**
   - Edit `app/Config/Database.php` sesuai koneksi MySQL lokal kamu.
   - Buat database baru, misal: `toko`.
4. **Migrasi & Seed Data**
   Jalankan migrasi dan seeder untuk membuat tabel dan data awal:
   ```bash
   php spark migrate
   php spark db:seed UserSeeder
   php spark db:seed ProductCategorySeeder
   php spark db:seed ProductSeeder
   # (opsional) php spark db:seed TransactionDetailSeeder
   ```
5. **Konfigurasi Environment**
   - Copy `.env.example` ke `.env` jika ada, lalu atur variabel seperti API_KEY, COST_KEY, dsb.
6. **Jalankan Server**
   ```bash
   php spark serve
   ```
   atau akses via XAMPP/htdocs jika menggunakan Windows.
7. **Akses Aplikasi**
   - Frontend: `http://localhost:8080/`
   - Dashboard Toko: `http://localhost/dashboard-toko/dashboard-toko/`

## Struktur Proyek

```
projekCI2/
├── app/
│   ├── Config/           # Konfigurasi aplikasi, database, routes, dsb
│   ├── Controllers/      # Controller utama (Auth, Produk, Transaksi, API, dsb)
│   ├── Database/
│   │   ├── Migrations/   # File migrasi tabel
│   │   └── Seeds/        # Seeder data awal
│   ├── Filters/          # Filter autentikasi, redirect
│   ├── Models/           # Model untuk produk, transaksi, user, dsb
│   ├── Views/            # View utama (produk, keranjang, checkout, login, dsb)
│   │   └── components/   # Komponen UI (header, footer, sidebar)
│   └── ...
├── public/
│   ├── index.php         # Entry point aplikasi
│   ├── dashboard-toko/   # Dashboard monitoring transaksi (standalone)
│   └── NiceAdmin/        # Asset template admin (CSS, JS, gambar)
├── writable/             # Cache, logs, uploads
├── composer.json         # Dependency PHP
├── README.md             # Dokumentasi proyek
└── ...
```

## Catatan Penting

- Untuk fitur dashboard-toko, data transaksi diambil via API internal dengan API key.
- Kolom "Jumlah Item" di dashboard menghitung total seluruh item pada detail transaksi.
- Fitur diskon otomatis aktif jika ada diskon pada tanggal hari ini.
- Semua aksi CRUD (produk, kategori, diskon) menggunakan modal dan AJAX, tanpa reload halaman.
- Tabel menggunakan DataTables untuk kemudahan pencarian dan navigasi data.

## Mekanisme Diskon Otomatis

- Jika ada diskon aktif pada hari ini (diskon harian), maka harga produk di keranjang dan checkout akan otomatis dikurangi nominal diskon tersebut.
- Nominal diskon yang berlaku akan tercatat di setiap detail transaksi pada saat checkout.
- Riwayat transaksi/profil user akan menampilkan badge diskon pada setiap produk yang mendapat diskon, sesuai nominal diskon hari itu.
- Diskon hanya berlaku untuk transaksi pada tanggal diskon aktif.

## Contoh Akun Login

| Role  | Username                                   | Password |
| ----- | ------------------------------------------ | -------- |
| Admin | admin                                      | admin123 |
| User  | user                                       | user123  |
| Guest | (tanpa login, bisa checkout sebagai guest) |

---

Jika ada pertanyaan atau bug, silakan hubungi developer atau buat issue di repository ini.
