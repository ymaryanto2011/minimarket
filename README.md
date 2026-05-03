# Minimarket POS System

Sistem Point of Sales (POS) modern untuk Minimarket dan Swalayan dengan dukungan scan barcode, harga bertingkat, manajemen stok, laporan penjualan, dan pengajuan penawaran barang.

## 🚀 Fitur Utama

- **Kasir POS** - Transaksi penjualan real-time dengan scan barcode
- **Harga Bertingkat** - Dukungan harga eceran dan grosir otomatis
- **Master Barang** - Kelola produk, kategori, dan harga
- **Manajemen Stok** - Stok masuk, keluar, penyesuaian, dan histori
- **Pengajuan Penawaran** - Buat penawaran dengan nomor otomatis, export PDF
- **Laporan Penjualan** - Laporan harian dan bulanan dengan breakdown metode pembayaran
- **Cetak Barcode** - Generate dan print label barcode
- **Setting Profil Toko** - Kelola nama dan alamat minimarket

## 📋 Prerequisites

- PHP 8.1+
- MySQL 8.0+
- Composer
- XAMPP (recommended untuk development lokal)

## 🛠️ Setup Project

### 1. Clone/Setup Repository

```bash
cd d:\xampp\htdocs\Minimarket
```

### 2. Install Dependencies (Future)

```bash
composer install
```

### 3. Setup Environment

```bash
cp .env.example .env
# Update DB credentials di .env
```

### 4. Generate App Key (Future)

```bash
php artisan key:generate
```

### 5. Run Migrations (Future)

```bash
php artisan migrate
```

### 6. Serve Application

```bash
php artisan serve
# Atau akses via: http://localhost/Minimarket/public
```

## 📁 Project Structure

```
Minimarket/
├── PRD_MINIMARKET_POS_SYSTEM.md      # Dokumentasi requirement produk
├── app/
│   ├── Http/Controllers/              # Controller (future implementation)
│   └── Models/                         # Model Eloquent (future implementation)
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php          # Main layout template
│       ├── dashboard/
│       │   └── index.blade.php        # Dashboard screen
│       ├── pos/
│       │   └── index.blade.php        # POS/Kasir screen
│       ├── master/
│       │   └── index.blade.php        # Master barang screen
│       ├── stock/
│       │   └── index.blade.php        # Stok barang screen
│       ├── quotation/
│       │   └── index.blade.php        # Penawaran barang screen
│       ├── report/
│       │   └── index.blade.php        # Laporan penjualan screen
│       ├── barcode/
│       │   └── index.blade.php        # Cetak barcode screen
│       └── setting/
│           └── index.blade.php        # Setting profil minimarket screen
├── public/
│   ├── css/
│   │   └── tailwind.css               # Tailwind CSS styling
│   └── js/                            # JavaScript (future)
├── routes/
│   └── web.php                        # Web routes definition
├── database/
│   └── migrations/                    # Database migrations (future)
├── composer.json                      # PHP dependencies
└── .env.example                       # Environment example
```

## 🎨 Frontend Stack

- **Framework**: Laravel 12+
- **Templating**: Blade Template
- **Styling**: Tailwind CSS
- **Interactivity**: Alpine.js (lightweight) atau React via Inertia (advanced)

## 📊 Database Schema (Fase Backend)

### Tabel Utama

- `store_profiles` - Profil minimarket (nama, alamat, telepon, email)
- `categories` - Kategori produk
- `products` - Master barang (kode, nama, harga eceran, harga grosir, qty min grosir)
- `stock_movements` - Histori mutasi stok (masuk, keluar, penyesuaian)
- `transactions` - Transaksi penjualan
- `transaction_items` - Item detail per transaksi
- `quotations` - Pengajuan penawaran barang
- `quotation_items` - Item detail per penawaran
- `users` - User kasir, supervisor, admin
- `audit_logs` - Audit trail perubahan

## 🔄 Tahap Implementasi

### ✅ Fase 1: Frontend (SELESAI)

- [x] Layout aplikasi utama dengan sidebar navigasi
- [x] Dashboard dengan ringkasan omzet harian
- [x] POS Screen untuk transaksi kasir
- [x] Master Barang dengan CRUD interface
- [x] Manajemen Stok dengan status alert
- [x] Pengajuan Penawaran Barang
- [x] Laporan Penjualan harian dan bulanan
- [x] Cetak Barcode dengan preview
- [x] Setting Profil Minimarket

### 🔄 Fase 2: Backend & Database (NEXT)

- [ ] Setup database dan migration Laravel
- [ ] Implementasi Model dan relasi data
- [ ] API Endpoint untuk setiap modul
- [ ] Business logic (transaksi, harga grosir, stok update)
- [ ] Authentication & Authorization
- [ ] Validasi input dan error handling

### 🔄 Fase 3: Integration & Testing (SETELAH FASE 2)

- [ ] Integrasi frontend dengan API backend
- [ ] Unit testing & integration testing
- [ ] Performance optimization
- [ ] Bug fixing dan polish UI
- [ ] User acceptance testing (UAT)

### 🔄 Fase 4: Deployment & Go-Live

- [ ] Setup database production
- [ ] Deploy ke hosting/server
- [ ] Training user dan dokumentasi
- [ ] Monitoring dan support

## 🎯 Konvensi Pengembangan

### Routes Naming

- Dashboard: `dashboard`
- Resource CRUD: `pos.index`, `pos.create`, `pos.edit`, `pos.destroy`

### Blade Naming

- Layout: `layouts/app.blade.php`
- Page: `section/page.blade.php` (e.g., `pos/index.blade.php`)

### CSS Classes (Tailwind)

- Button: `.btn-primary`, `.btn-secondary`, `.btn-success`, `.btn-danger`
- Input: `.input-field`
- Card: `.card`, `.card-header`
- Badge: `.badge-info`, `.badge-success`, `.badge-warning`, `.badge-danger`

## 📚 Dokumentasi Lengkap

Dokumentasi lengkap requirement produk tersedia di: **PRD_MINIMARKET_POS_SYSTEM.md**

Dokumen ini mencakup:

- Latar belakang & tujuan produk
- Ruang lingkup fitur detail
- Persona dan aktor pengguna
- Kebutuhan fungsional per modul
- Kebutuhan non-fungsional (performa, security, reliability)
- Asumsi teknis dan batasan
- Wireframe high-level
- Metrik keberhasilan

## 🔗 Links & Resources

- **Laravel Documentation**: https://laravel.com/docs
- **Tailwind CSS**: https://tailwindcss.com
- **Alpine.js**: https://alpinejs.dev

## 📝 Notes

- Sistem dirancang untuk single-outlet terlebih dahulu
- Harga grosir berbasis quantity minimum per produk
- Semua transaksi bersifat final (tidak bisa dihapus, hanya dibatalkan dengan catatan)
- Data backup harus dilakukan rutin

## 📞 Support

Untuk pertanyaan atau masalah teknis, silakan hubungi admin POS system.

---

**Last Updated**: 2 Mei 2026  
**Version**: 1.0 (Frontend Complete)  
**Next Phase**: Backend & Database Implementation
