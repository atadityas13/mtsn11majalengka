#!/usr/bin/env bash
# Susunan andalan cPanel (document root tetap public_html):
#   ~/mtsn11majalengka/   ← kode Laravel (app, vendor, …)
#   ~/public_html/        ← HANYA isi folder public/ (+ index.php yang mengarah ke atas)
#
# Jalankan sekali di Terminal cPanel (saat Laravel masih di ~/public_html):
#   bash scripts/cpanel-flatten-to-public-html.sh

set -euo pipefail

HOME_DIR="${HOME:?}"
OLD="$HOME_DIR/public_html"
APP="$HOME_DIR/mtsn11majalengka"
WEB="$HOME_DIR/public_html"

if [[ -L "$OLD" ]]; then
  echo "public_html masih symlink. Lepas dulu atau sesuaikan manual."
  ls -la "$OLD"
  exit 1
fi

if [[ ! -f "$OLD/public/index.php" ]]; then
  echo "Tidak menemukan $OLD/public/index.php"
  echo "Script ini HANYA untuk migrasi sekali: project Laravel utuh masih di dalam public_html."
  echo
  if [[ -f "$APP/artisan" && -f "$WEB/index.php" && ! -f "$WEB/public/index.php" ]]; then
    echo "Layout flatten sudah aktif. Setelah git pull, jalankan:"
    echo "  bash $APP/scripts/cpanel-sync-public-html.sh"
  fi
  exit 1
fi

if [[ -e "$APP" ]]; then
  echo "Folder $APP sudah ada. Backup/pindahkan dulu agar tidak tertimpa."
  exit 1
fi

echo "==> Memindahkan project ke $APP"
mv "$OLD" "$APP"

echo "==> Membuat public_html baru dari isi public/"
mkdir -p "$WEB"
cp -a "$APP/public/." "$WEB/"

echo "==> Menulis index.php agar bootstrap ke ../mtsn11majalengka"
cat > "$WEB/index.php" <<'PHP'
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$base = dirname(__DIR__).'/mtsn11majalengka';

if (file_exists($maintenance = $base.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $base.'/vendor/autoload.php';

/** @var Application $app */
$app = require_once $base.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
PHP

echo "==> Menulis .htaccess Laravel (+ placeholder handler cPanel)"
cat > "$WEB/.htaccess" <<'HTA'
DirectoryIndex index.php

<IfModule mod_rewrite.c>
    RewriteEngine On

    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    RewriteCond %{HTTP:x-xsrf-token} .
    RewriteRule .* - [E=HTTP_X_XSRF_TOKEN:%{HTTP:X-XSRF-Token}]

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# php -- BEGIN cPanel-generated handler, do not edit
# Set the “ea-php83” package as the default “PHP” programming language.
<IfModule mime_module>
  AddHandler application/x-httpd-ea-php83 .php .php8 .phtml
</IfModule>
# php -- END cPanel-generated handler, do not edit
HTA

# Cadangan untuk dipulihkan jika MultiPHP menimpa seluruh file
cp "$WEB/.htaccess" "$WEB/.htaccess.backup"
cp "$APP/scripts/ensure-public-html-htaccess.sh" "$WEB/../mtsn11majalengka/scripts/" 2>/dev/null || true

echo "==> Storage link (wajib agar gambar tampil)"
rm -rf "$WEB/storage"
ln -s "$APP/storage/app/public" "$WEB/storage"
ls -la "$WEB/storage"
echo
echo "Selesai."
echo "Cek: https://mtsn11majalengka.sch.id/"
echo "Update kode: cd ~/mtsn11majalengka && git pull"
echo "Lalu salin ulang aset public bila perlu:"
echo "  cp -a ~/mtsn11majalengka/public/build ~/public_html/"
echo
echo "Pasang cron (setiap 5 menit) agar .htaccess tidak 'hilang' rewrite-nya:"
echo "  */5 * * * * bash $APP/scripts/ensure-public-html-htaccess.sh >/dev/null 2>&1"
