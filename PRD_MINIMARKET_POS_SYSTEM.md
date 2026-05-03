# PRD - Minimarket Point of Sales (POS) System

**Dokumen Requirement Produk untuk Sistem POS Minimarket/Swalayan**

---

## 1. LATAR BELAKANG & TUJUAN PRODUK

### 1.1 Latar Belakang

Minimarket dan toko swalayan membutuhkan sistem manajemen transaksi penjualan yang efisien, cepat, dan dapat mengelola berbagai tingkat harga (eceran/retail dan grosir) serta inventaris barang dengan mudah. Sistem POS modern dapat meningkatkan kecepatan transaksi, akurasi data, dan memberikan laporan real-time untuk pengambilan keputusan.

### 1.2 Tujuan Produk

Mengembangkan sistem Point of Sales (POS) web-based yang:

- Mempercepat proses transaksi penjualan dengan antarmuka kasir intuitif
- Mendukung scan barcode untuk pencarian dan checkout cepat
- Mengelola harga bertingkat (eceran/retail dan grosir) otomatis
- Menyediakan laporan penjualan real-time per hari dan per bulan
- Memfasilitasi pengajuan penawaran barang kepada supplier atau pelanggan
- Memudahkan pencetakan barcode untuk label barang baru atau reprint
- Mengelola stok barang dan peringatan stok minimum
- Memberikan transparansi informasi minimarket melalui profil toko

### 1.3 Target Pengguna

- **Kasir**: Melakukan transaksi penjualan, scan barcode, mencetak struk
- **Manager/Pemilik Toko**: Memantau laporan penjualan, mengelola harga dan stok
- **Staf Gudang/Inventory**: Input stok masuk/keluar, penyesuaian stok
- **Admin/Supervisor**: Mengelola pengajuan penawaran barang, setting aplikasi, profil minimarket

---

## 2. RUANG LINGKUP PRODUK

### 2.1 Fitur Utama

1. **Module POS/Kasir** - Transaksi penjualan real-time dengan scan barcode
2. **Master Barang** - Data produk, harga eceran/grosir, kategori
3. **Manajemen Stok** - Stok masuk, keluar, penyesuaian, histori mutasi
4. **Harga Bertingkat** - Sistem harga eceran dan grosir dengan aturan quantity minimum
5. **Pengajuan Penawaran Barang** - Buat penawaran dengan nomor otomatis, tujuan customer, export PDF
6. **Laporan Penjualan** - Laporan harian dan bulanan dengan ringkasan omzet, item, metrik
7. **Cetak Barcode** - Generate dan print label barcode untuk item baru atau reprint
8. **Setting Profil Minimarket** - Kelola nama toko dan alamat, tampil di header aplikasi dan dokumen

### 2.2 Scope Tidak Termasuk (Fase Pertama)

- Multi-outlet/cabang
- Sistem akuntansi/finansial kompleks (PPh, PPN detail)
- Integrasi e-commerce atau online sales
- Sistem loyalty program atau pelanggan member berbayar
- Management pengguna dengan role-based access yang sangat granular (phase 2)
- Mobile app native (hanya responsive web terlebih dahulu)

---

## 3. PERSONA & AKTOR PENGGUNA

| Persona           | Deskripsi                   | Aktivitas Utama                                                                          |
| ----------------- | --------------------------- | ---------------------------------------------------------------------------------------- |
| **Kasir**         | Operator transaksi di kasir | Scan barcode, input qty, terima pembayaran, cetak struk, hold/resume transaksi           |
| **Supervisor**    | Pengawas toko               | Lihat laporan real-time, ubah harga, approval penawaran barang, cetak struk ulang        |
| **Manajer Stok**  | Pengelola inventaris        | Input stok masuk/keluar, penyesuaian, lihat histori, set stok minimum                    |
| **Admin/Pemilik** | Administrator sistem        | Setting profil minimarket, setting harga master, lihat laporan, manajemen user (phase 2) |

---

## 4. KEBUTUHAN FUNGSIONAL DETAIL

### 4.1 Module POS/Kasir

#### Alur Transaksi Penjualan:

1. **Pencarian Barang**
   - Scan barcode menggunakan scanner fisik atau input manual
   - Pencarian berdasarkan kode barang, nama barang
   - Tampil daftar item sesuai kriteria pencarian
   - Pilih item untuk ditambahkan ke keranjang

2. **Keranjang Penjualan**
   - Tampil daftar item di keranjang dengan qty, harga satuan, total
   - Edit qty item (naik/turun/hapus)
   - Hitung subtotal otomatis
   - Hitung total pajak jika ada aturan pajak (configurable)
   - Hitung total akhir

3. **Diskon & Promosi**
   - Diskon per item atau per transaksi (nominal atau persen)
   - Validasi diskon tidak melebihi harga item
   - Hitung ulang total setelah diskon

4. **Metode Pembayaran**
   - Tunai
   - Kartu (debit/kredit)
   - E-wallet (optional phase 2)
   - Pilih metode, input jumlah bayar, hitung kembalian (jika tunai)

5. **Fitur Khusus Kasir**
   - Hold/Resume transaksi - jeda transaksi tanpa checkout, resume kemudian
   - Batalkan transaksi
   - Cetak struk dengan format standar (toko, tanggal, item, total, metode pembayaran)
   - Struk harus mencantumkan nama dan alamat minimarket dari setting profil

#### Harga Otomatis Berdasarkan Quantity (Retail vs Grosir):

- **Harga Eceran/Retail**: Dipakai untuk qty < threshold grosir
- **Harga Grosir**: Dipakai otomatis ketika qty >= threshold quantity minimum grosir
- Hitung ulang harga ketika qty berubah
- Tampilkan indikasi harga berlaku (Eceran/Grosir)

### 4.2 Master Barang

- **Kode Barang** - Unique identifier, dapat berupa barcode
- **Nama Barang** - Deskripsi produk
- **Kategori** - Pengelompokan produk
- **Harga Eceran** - Harga satuan untuk penjualan eceran
- **Harga Grosir** - Harga satuan untuk penjualan grosir
- **Qty Minimum Grosir** - Threshold quantity untuk apply harga grosir
- **Stok Saat Ini** - Sinkron dengan module stok
- **Stok Minimum** - Alert jika stok di bawah nilai ini
- **Satuan** - pcs, pack, box, kg, liter, dll
- **Foto/Gambar** - Optional, untuk visual di POS
- **Status Aktif/Nonaktif** - Barang aktif atau tidak dijual
- **Tanggal Dibuat/Diubah** - Audit trail

### 4.3 Manajemen Stok

#### Tipe Mutasi Stok:

1. **Stok Masuk** - Input stok dari supplier, kembalian pelanggan, atau koreksi naik
   - Kode referensi (PO, nota supplier, dll)
   - Tanggal masuk
   - Keterangan
   - Histori lengkap

2. **Stok Keluar** - Stok otomatis berkurang saat transaksi penjualan
   - Dicatat otomatis dari module POS
   - Tanggal, kode barang, qty, nomor invoice penjualan

3. **Penyesuaian Stok** - Koreksi stok karena rusak, hilang, atau recount
   - Qty penyesuaian (naik/turun)
   - Keterangan (alasan)
   - Tanggal penyesuaian

4. **Histori Mutasi Stok** - Laporan pergerakan stok dengan detail:
   - Tanggal, kode barang, tipe mutasi, qty, stok awal, stok akhir, keterangan

#### Alert & Monitoring:

- Alert jika stok item < stok minimum
- Laporan stok minimum untuk planning pembelian
- Laporan fast-moving dan slow-moving items

### 4.4 Harga Bertingkat (Eceran vs Grosir)

- Master harga per barang terdiri dari **Harga Eceran** dan **Harga Grosir**
- Aturan trigger grosir: Quantity Minimum Grosir
- Ketika kasir input qty barang:
  - Jika qty < qty_min_grosir → gunakan Harga Eceran
  - Jika qty >= qty_min_grosir → gunakan Harga Grosir otomatis
- Grosir bisa dikustomisasi per barang atau per kategori (phase 2)

### 4.5 Pengajuan Penawaran Barang

#### Fitur:

1. **Nomor Penawaran Otomatis** - Generate format: PW-YYYYMMDD-XXXX (auto increment per hari)
2. **Kepada/Customer** - Input nama customer atau supplier tujuan penawaran
3. **Tanggal Penawaran** - Auto isi dengan tanggal hari ini
4. **Detail Item Penawaran** - Tabel item: kode barang, nama, qty, harga satuan, total
5. **Diskon Penawaran** - Diskon total penawaran (nominal atau persen)
6. **Total Penawaran** - Subtotal - diskon
7. **Keterangan/Catatan** - Note tambahan, syarat pembayaran, dll
8. **Status** - Draft, Submit, Approved, Rejected, atau Expired
9. **Export/Print PDF** - Generate PDF penawaran dengan format profesional:
   - Header: Logo/Nama minimarket, alamat dari setting profil
   - Body: Nomor penawaran, tanggal, kepada, tabel item, total, catatan
   - Footer: Validitas penawaran, kontak, tandatangan (manual)
10. **Histori Penawaran** - Simpan semua penawaran dengan tanggal dibuat, diubah, status terakhir

### 4.6 Laporan Penjualan

#### Laporan Harian:

- Tanggal laporan
- Jumlah transaksi
- Total omzet (jumlah uang terjual)
- Item terjual (jumlah unit dan jumlah variasi item)
- Breakdown metode pembayaran (tunai, kartu, dll) - optional
- Breakdown per kasir (siapa saja transaksi) - optional
- Diskon total yang diberikan

#### Laporan Bulanan:

- Periode bulan (Januari, Februari, dll)
- Jumlah transaksi total
- Total omzet bulan ini
- Rata-rata transaksi per hari
- Item terjual terbanyak (top 10)
- Tren harian (grafik omzet per hari)
- Metode pembayaran breakdown - optional

#### Filter & Export:

- Filter by tanggal range, kasir, metode pembayaran
- Export ke PDF, Excel

### 4.7 Cetak Barcode

#### Fitur:

1. **Generate Barcode** - Input kode barang atau pilih dari master:
   - Generate barcode image (CODE128, EAN-13, atau format lain)
   - Qty label yang ingin dicetak
   - Tata letak label (1x1, 2x3, dll per halaman)
   - Font ukuran kode, nama barang, harga (optional)

2. **Reprint Label** - Cetak ulang label untuk barang yang sudah ada
   - Pilih barang dari master
   - Qty label yang ingin dicetak

3. **Setting Printer** - Konfigurasi:
   - Ukuran kertas label
   - Margin
   - Format barcode yang digunakan

4. **Preview Sebelum Print** - Lihat preview label sebelum cetak

### 4.8 Setting Profil Minimarket

#### Field:

- **Nama Minimarket** - Nama toko/bisnis (misal: "Minimarket Indo Jaya")
- **Alamat Lengkap** - Alamat fisik toko (misal: "Jl. Merdeka No. 123, Jakarta Selatan 12345")
- **Nomor Telepon** - Kontak toko (optional)
- **Email** - Email toko (optional)
- **Logo** - Unggah logo untuk header aplikasi dan dokumen (optional phase 2)

#### Penggunaan Data Profil:

- Header aplikasi: Tampilkan nama minimarket
- Struk penjualan: Header dengan nama dan alamat minimarket
- Dokumen penawaran: Header dengan nama dan alamat minimarket
- Dashboard: Informasi toko utama

---

## 5. KEBUTUHAN NON-FUNGSIONAL

### 5.1 Performa

- **Response Time POS**: < 1 detik untuk scan barcode dan tampil item
- **Laporan Harian**: Generate < 5 detik
- **Concurrent Users**: Minimal 3-5 kasir bersamaan

### 5.2 Keamanan & Audit

- Login user dengan password (role-based access phase 2)
- Audit trail untuk perubahan harga, stok, penawaran
- Tidak boleh ada penghapusan transaksi, hanya pembatalan dengan catatan
- Validasi input untuk mencegah input negatif atau tidak valid

### 5.3 Reliabilitas

- Backup data rutin (daily backup recommended)
- Database transaction untuk memastikan integritas transaksi penjualan
- Recovery procedure jika sistem crash (no loss of transaction data)

### 5.4 Usability

- Antarmuka intuitif untuk kasir (training minimal)
- Keyboard shortcut untuk action cepat (ESC batal, Enter next, dll)
- Responsive design untuk berbagai ukuran monitor/tablet

### 5.5 Data Integrity

- Validasi stok saat transaksi (tidak boleh oversell)
- Validasi harga master (tidak boleh negatif)
- Foreign key constraints untuk relasi data

---

## 6. ASUMSI TEKNIS & BATASAN

### 6.1 Perangkat & Infrastruktur

- **Server**: Lokal XAMPP di d:\xampp\htdocs\Minimarket
- **Database**: MySQL (sudah tersedia di XAMPP)
- **Barcode Scanner**: Hardware scanner USB yang kompatibel dengan input keyboard
- **Printer Struk**: Thermal receipt printer (sesuai driver Windows)
- **Printer Label**: Label printer untuk barcode (optional, USB compatible)
- **Internet**: Tidak wajib koneksi internet untuk operasional lokal (offline-first)

### 6.2 Batasan Fase Pertama

- Single outlet/cabang saja (multi-outlet phase 2)
- Role-based access control dasar (admin, kasir, supervisor)
- Laporan simple, tidak termasuk accounting detail
- Tidak ada integrase payment gateway online
- No support untuk POS mobile native (responsive web saja)

### 6.3 Format & Standar

- **Tanggal**: DD-MMM-YYYY atau DD/MM/YYYY (Indonesia format)
- **Mata Uang**: Rupiah (Rp)
- **Bahasa Interface**: Bahasa Indonesia
- **Timezone**: WIB (UTC+7)

---

## 7. MODUL APLIKASI & ARSITEKTUR HIGH-LEVEL

### 7.1 Struktur Modul Frontend

```
Frontend/UI (Responsif Web)
├── Login/Dashboard
├── POS/Kasir
│   ├── Pencarian Barang (Scan/Manual)
│   ├── Keranjang Penjualan
│   ├── Checkout & Pembayaran
│   └── Cetak Struk
├── Master Data
│   ├── Master Barang
│   ├── Manajemen Stok (Stok Masuk, Keluar, Penyesuaian)
│   └── Histori Mutasi Stok
├── Pengajuan Penawaran Barang
│   ├── Buat/Edit Penawaran
│   ├── List Penawaran
│   └── Export/Print PDF
├── Laporan Penjualan
│   ├── Laporan Harian
│   ├── Laporan Bulanan
│   └── Filter & Export
├── Cetak Barcode
│   ├── Generate Label
│   └── Reprint
└── Setting
    ├── Profil Minimarket
    └── User & Permission (phase 2)
```

### 7.2 Entitas Data Utama

- **Products** - Master barang
- **Categories** - Kategori produk
- **Pricing** - Harga retail/grosir per barang
- **Stock** - Stok saat ini dan histori mutasi
- **Transactions** - Transaksi penjualan
- **Transaction_Items** - Item detail per transaksi
- **Discounts** - Diskon per item/transaksi
- **Quotations** - Pengajuan penawaran barang
- **Quotation_Items** - Item detail per penawaran
- **Store_Profile** - Profil minimarket (nama, alamat)
- **Users** - User kasir, supervisor, admin
- **Audit_Log** - Audit trail perubahan

### 7.3 Alur Data

1. **Input Barang** → Master Products → Update Stock → POS Display
2. **Transaksi POS** → Transaction + Transaction_Items → Update Stock → Laporan
3. **Pengajuan Penawaran** → Quotations + Quotation_Items → PDF Export
4. **Cetak Barcode** → Products → Barcode Image → Print

---

## 8. PRIORITAS FITUR & ROADMAP IMPLEMENTASI

### 8.1 Fase 1 (MVP - Frontend Priority)

**Durasi: 2-3 minggu (frontend mockup + basic backend)**

Prioritas fitur (urutan pengerjaan):

1. Layout & Dashboard utama (tampilan aplikasi)
2. POS Screen & Kasir (inti bisnis)
3. Master Barang & Stok (data setup)
4. Cetak Struk (output transaksi)
5. Laporan Penjualan Simple (monitoring)
6. Setting Profil Minimarket

Backend:

- Database setup dan migration
- API basic untuk CRUD barang, transaksi, stok
- Proses transaksi dan update stok

### 8.2 Fase 2 (Laporan & Pengajuan Penawaran)

**Durasi: 1-2 minggu**

Prioritas fitur:

1. Pengajuan Penawaran Barang (buat, list, PDF export)
2. Laporan Penjualan Detail (harian & bulanan)
3. Cetak Barcode (generate & print label)

Backend:

- API untuk penawaran barang
- PDF generator
- Barcode generator

### 8.3 Fase 3 (Polish & Advanced Features)

**Durasi: 1+ minggu**

Prioritas fitur:

1. Role-based access control & multi-user
2. Performa optimization
3. Backup & security hardening
4. Advanced laporan (grafik, export Excel)

---

## 9. STRATEGI DELIVERY & IMPLEMENTASI

### 9.1 Frontend-First Approach

Implementasi dimulai dari **tampilan (UI/Frontend) terlebih dahulu**, baru turun ke backend dan database:

1. **Phase Frontend Mockup** (1-2 minggu)
   - Design layout aplikasi dengan HTML/CSS (Tailwind CSS)
   - Build template/component frontend untuk setiap screen
   - Setup routing/navigasi antar halaman
   - Mock data untuk preview tampilan
   - Approval dari user terhadap tampilan & UX

2. **Phase Backend & Database** (2-3 minggu setelah frontend disetujui)
   - Buat database schema dan migration
   - Build API/backend service untuk setiap modul
   - Integrasi frontend dengan API
   - Testing & bug fixing

3. **Phase Integration & Testing** (1 minggu)
   - End-to-end testing
   - User acceptance testing (UAT)
   - Fine-tuning & optimization

### 9.2 Teknologi yang Direkomendasikan

#### Rekomendasi Utama: **Laravel + Blade/Livewire + Tailwind CSS**

| Aspek                 | Pilihan                                          | Alasan                                                               |
| --------------------- | ------------------------------------------------ | -------------------------------------------------------------------- |
| **Backend Framework** | Laravel 12+                                      | PHP-based, sesuai XAMPP, produktif, banyak package ecosystem         |
| **Frontend**          | Blade Template + Livewire (atau Inertia + React) | Rapid development, real-time component, modern UI dengan Tailwind    |
| **Styling**           | Tailwind CSS                                     | Utility-first, rapid prototyping, responsive by default, modern look |
| **Database**          | MySQL 8+ (XAMPP default)                         | Relasi data solid, backup mudah, sudah tersedia                      |
| **JavaScript**        | Alpine.js (lightweight) atau React (Inertia)     | Interaktif UI, scan event handling, real-time update                 |

#### Opsi Alternatif (Jika Prioritas UI Sangat Interaktif):

- **Next.js + Supabase** - Full-stack JavaScript, modern UX, database-as-service
- **Vue.js 3 + Laravel** - Alternatif lightweight dengan Laravel backend

#### Alasan Pemilihan Laravel:

✅ Sesuai dengan environment XAMPP (PHP)  
✅ Cepat development dengan Artisan CLI  
✅ ORM Eloquent untuk relasi data kompleks  
✅ Built-in authentication & authorization  
✅ Package rich (PDF, Barcode, Excel export)  
✅ Mudah di-deploy ke shared hosting  
✅ Dokumentasi lengkap & komunitas besar

---

## 10. USER INTERFACE WIREFRAME (HIGH-LEVEL)

### 10.1 Main Dashboard

```
┌─────────────────────────────────────────────────┐
│  Minimarket Indo Jaya        [Logout]  [Setting] │
├─────────────────────────────────────────────────┤
│  Dashboard                                       │
│  ┌────────────────┬────────────────┬──────────┐  │
│  │ Omzet Hari     │ Transaksi Hari │ Item Terjual
│  │ Rp. 5.234.000  │ 42 transaksi   │ 187 unit
│  └────────────────┴────────────────┴──────────┘  │
│                                                  │
│  [← POS]  [Master Barang]  [Stok]  [Laporan]   │
│  [Penawaran]  [Cetak Barcode]  [Setting]        │
└─────────────────────────────────────────────────┘
```

### 10.2 POS/Kasir Screen

```
┌────────────────────────────────────────────────┐
│ KASIR - Minimarket Indo Jaya                  │
├─────────────────────┬──────────────────────────┤
│ Cari/Scan Barcode:  │ KERANJANG PENJUALAN     │
│ [________] [Cari]   │ ┌────────────────────┐  │
│                     │ │ Kode │ Item │ Qty │ Harga │ Total
│ Item Hasil Pencarian│ │─────┼──────┼─────┼────────┤
│ ┌─────────────────┐ │ │ B001│ Indomie│ 10 │ 2.000 │ 20.000
│ │ B001│ Indomie   │ │ │ B002│ Teh   │ 5  │ 1.500 │ 7.500
│ │ B002│ Teh Botol │ │ └────────────────────┘
│ └─────────────────┘ │ Subtotal: 27.500
│                     │ Diskon: [___] %
│                     │ TOTAL: Rp. 27.500
│                     │
│                     │ [Metode Pembayaran ↓]
│                     │ [Hold] [Batalkan] [Bayar]
└────────────────────┴──────────────────────────┘
```

### 10.3 Master Barang

```
┌────────────────────────────────────────────┐
│ MASTER BARANG - Minimarket Indo Jaya       │
├────────────────────────────────────────────┤
│ [+ Tambah Barang] [Cari: _____] [Filter]   │
├────────────────────────────────────────────┤
│ Kode │ Nama │ Kategori │ Harga Eceran │ Harga Grosir │ Stok
│─────┼──────┼──────────┼──────────────┼──────────────┼─────
│ B001│Indomie│Makanan   │ 2.000        │ 1.700 (>12) │ 145
│ B002│Teh    │Minuman   │ 1.500        │ 1.200 (>20) │ 87
└────────────────────────────────────────────┘
```

### 10.4 Setting Profil Minimarket

```
┌────────────────────────────────────────────┐
│ SETTING PROFIL MINIMARKET                  │
├────────────────────────────────────────────┤
│ Nama Minimarket: [Minimarket Indo Jaya___] │
│                                            │
│ Alamat Lengkap:                            │
│ [Jl. Merdeka No. 123,                     │
│  Jakarta Selatan 12345________________]    │
│                                            │
│ Nomor Telepon: [(021) 555-1234_________]  │
│ Email: [indojaya@email.com______________] │
│                                            │
│ [Simpan] [Batal]                          │
└────────────────────────────────────────────┘
```

---

## 11. METRIK KEBERHASILAN & KPI

| KPI                     | Target                             | Metode Ukur                             |
| ----------------------- | ---------------------------------- | --------------------------------------- |
| **Transaksi per Hari**  | +30% lebih cepat dibanding manual  | Durasi transaksi (scan → bayar → struk) |
| **Akurasi Stok**        | 99%+ match antara sistem dan fisik | Inventory count vs sistem               |
| **Ketersediaan Sistem** | 99%+ uptime                        | Monitoring log                          |
| **User Satisfaction**   | 4.5/5 rating                       | Feedback form sederhana                 |
| **Laporan Accuracy**    | 100% match transaksi dan laporan   | Audit trail                             |

---

## 12. CATATAN & NEXT STEPS

### Catatan Penting:

- Sistem ini dirancang untuk single-outlet pertama kali, multi-outlet bisa difasakan di versi 2.0
- Harga grosir berbasis quantity minimum (bisa diperluas ke customer tier di fase 2)
- Transaksi bersifat final (tidak bisa dihapus, hanya dibatalkan dengan catatan)
- Semua data harus di-backup rutin

### Next Steps:

1. ✅ Finalisasi PRD ini dan persetujuan stakeholder
2. 🔄 Implementasi Frontend (layout, POS screen, master barang, dll)
3. 🔄 Implementasi Backend & Database
4. 🔄 Integration & Testing
5. 🔄 UAT dengan kasir & manager
6. 🔄 Go-live & training

---

**Dokumen ini adalah panduan lengkap untuk pengembangan sistem POS Minimarket. Setiap perubahan requirement harus didiskusikan dan diperbarui di dokumen ini.**

_Last Updated: 2 Mei 2026_
_Version: 1.0_
