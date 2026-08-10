# MTsN 11 Majalengka — Situs + CMS

Situs resmi MTsN 11 Majalengka (Laravel + Filament). Konten dan tampilan dikelola dari panel admin `/admin`.

## Stack

- Laravel 13 + PHP 8.4
- Filament 5 (CMS admin)
- MySQL/SQLite + Blade + Tailwind CSS 4
- Deploy native PHP (git pull, tanpa restart Node app)

## Lokal (development)

```bash
composer install
cp .env.example .env   # jika belum ada
php artisan key:generate
# SQLite default sudah siap, atau set DB_* ke MySQL
php artisan migrate --seed
php artisan storage:link
npm install
npm run build
php artisan serve
```

- Situs: http://127.0.0.1:8000  
- Admin: http://127.0.0.1:8000/admin  
- Login awal: `admin@mtsn11majalengka.sch.id` / `password` (**ganti segera**)

## Deploy cPanel (native PHP)

1. Clone/pull repo ke folder hosting (mis. `~/mtsn11majalengka`)
2. Document root domain → `.../public`
3. Salin `.env.example` → `.env`, isi:
   - `APP_URL`
   - `DB_CONNECTION=mysql` + kredensial MySQL cPanel
4. Di SSH / Terminal cPanel:

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force   # hanya pertama kali
php artisan storage:link
npm install && npm run build  # atau build di lokal lalu upload public/build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

5. Pastikan folder `storage/` dan `bootstrap/cache/` writable

### Alur update kode

```bash
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
npm run build   # jika ada perubahan frontend
php artisan optimize
```

Tidak perlu Create Application Node / Restart app.

## Modul CMS

- Pengaturan Situs (identitas, hero, sambutan, kontak, tautan layanan, warna)
- Berita, Pengumuman, Agenda, Galeri
- Halaman (profil, akademik, dll.)
- Menu navigasi

## GitHub

https://github.com/atadityas13/mtsn11majalengka.git
