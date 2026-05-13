# Panduan Deploy ke Production — Minimarket POS

# Laravel 12 · PHP 8.1+ · MySQL · Apache/Nginx

---

## A. PERSIAPAN DI KOMPUTER LOKAL

Lakukan ini **sebelum upload** ke server:

```bash
# Pastikan dependensi production saja (tanpa dev tools)
composer install --no-dev --optimize-autoloader

# Build asset CSS/JS final
npm run build

# Bersihkan log lokal sebelum upload
del storage\logs\*.log
```

**Jangan ikutkan** folder/file berikut saat upload:

- `node_modules/`
- `.git/`
- `.env` (file env lokal)
- `storage/logs/*.log`
- `storage/app/public/` (akan dibuat ulang via artisan)

---

## B. STRUKTUR FOLDER DI SERVER

```
/home/username/               ← root akun hosting
    minimarket/               ← upload semua file proyek di sini
        app/
        bootstrap/
        config/
        database/
        public/               ← ini yang dijadikan document root domain
        resources/
        routes/
        storage/
        vendor/
        .env                  ← buat dari .env.production
        artisan
        ...
    public_html/              ← document root domain (shared hosting)
```

> **Shared Hosting:** Isi `public_html/` hanya dengan isi folder `public/` proyek,
> lalu sesuaikan `index.php` agar path `__DIR__` mengarah ke folder proyek.

---

## C. LANGKAH-LANGKAH DI SERVER (urutan wajib diikuti)

### 1. Siapkan file .env

```bash
cp .env.production .env
nano .env        # atau edit via File Manager cPanel
```

Wajib diisi:

- `APP_KEY` — kosong dulu, akan diisi langkah 2
- `APP_URL` — ganti dengan domain asli (https://)
- `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `STORE_NAME`, `STORE_ADDRESS`, `STORE_PHONE`

### 2. Generate APP_KEY

```bash
php artisan key:generate
```

> **Jangan skip!** Tanpa APP_KEY yang valid, session dan enkripsi akan rusak.

### 3. Jalankan Migrasi Database

```bash
php artisan migrate --force
```

> Flag `--force` diperlukan karena environment `production` akan meminta konfirmasi.

### 4. Buat Symlink Storage

```bash
php artisan storage:link
```

> Perintah ini membuat `public/storage` → `storage/app/public` agar file upload bisa diakses.

### 5. Optimasi Cache (wajib untuk performa)

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### 6. Set Permission Folder

```bash
# Linux/VPS
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

> Shared hosting cPanel: biasanya sudah otomatis, tidak perlu dijalankan.

---

## D. KONFIGURASI WEBSERVER

### Apache (sudah ada `public/.htaccess` ✓)

Pastikan di file konfigurasi vhost Apache:

```apache
<Directory /path/to/minimarket/public>
    AllowOverride All
    Require all granted
</Directory>
```

### Nginx

```nginx
server {
    listen 443 ssl;
    server_name yourdomain.com;
    root /var/www/minimarket/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Blokir akses ke file sensitif
    location ~ /\.(env|git|htaccess) {
        deny all;
    }
}
```

---

## E. SECURITY CHECKLIST ✓

Verifikasi satu per satu sebelum go-live:

- [ ] `APP_DEBUG=false` sudah diset di `.env`
- [ ] `APP_ENV=production` sudah diset di `.env`
- [ ] Password database bukan root tanpa password
- [ ] File `.env` tidak bisa diakses dari browser
      Test: buka `https://yourdomain.com/.env` → harus muncul 403 atau 404
- [ ] Folder `storage/` tidak bisa di-browse dari browser
- [ ] HTTPS aktif (SSL certificate terpasang, bisa pakai Let's Encrypt gratis)
- [ ] `SESSION_SECURE_COOKIE=true` sudah diset (wajib jika HTTPS)
- [ ] Tidak ada user dengan password kosong di tabel `users`

---

## F. TEST FUNGSIONAL SETELAH DEPLOY

Uji semua fitur ini setelah deploy:

- [ ] Login dengan role **Admin** — akses semua menu
- [ ] Login dengan role **Supervisor** — tidak bisa akses Master Barang
- [ ] Login dengan role **Kasir** — hanya bisa akses POS
- [ ] Transaksi POS berjalan normal, struk muncul
- [ ] Export Excel laporan harian & bulanan berhasil didownload
- [ ] Cetak PDF penawaran/quotation berhasil
- [ ] Import produk via Excel berfungsi
- [ ] Cetak barcode bisa digenerate
- [ ] Backup database dari menu Admin berhasil
- [ ] PWA bisa diinstall di HP (muncul banner "Pasang")

---

## G. TROUBLESHOOTING

### Error 500 setelah deploy

```bash
# Lihat detail error
tail -50 storage/logs/laravel.log

# Clear semua cache lalu rebuild
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize
```

### Halaman kosong / CSS tidak muncul

```bash
# Pastikan build sudah dijalankan
npm run build

# Cek permission
chmod -R 755 public/css public/js
```

### File upload tidak tersimpan

```bash
php artisan storage:link
chmod -R 775 storage/app/public
```

### Migration error "Table already exists"

```bash
# Cek status migrasi
php artisan migrate:status

# Hanya jalankan migrasi yang belum dijalankan
php artisan migrate --force
```

---

## H. RINGKASAN PERINTAH CEPAT

```bash
# Satu kali saat pertama deploy
cp .env.production .env && nano .env
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan optimize

# Setiap kali ada update kode
php artisan config:clear
php artisan optimize
```
