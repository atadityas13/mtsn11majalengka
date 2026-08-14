#!/usr/bin/env bash
# Setelah flatten: sinkronkan aset public → ~/public_html tanpa menimpa index.php khusus.
#
# Susunan yang diharapkan:
#   ~/mtsn11majalengka/   ← kode Laravel (git pull di sini)
#   ~/public_html/        ← document root (build, .htaccess, index.php → app)
#
# Pakai setelah update kode:
#   cd ~/mtsn11majalengka && git pull && bash scripts/cpanel-sync-public-html.sh

set -euo pipefail

HOME_DIR="${HOME:?}"
APP="$HOME_DIR/mtsn11majalengka"
WEB="$HOME_DIR/public_html"
SRC="$APP/public"

if [[ ! -f "$APP/artisan" ]]; then
  echo "ERROR: tidak ada Laravel di $APP"
  exit 1
fi

if [[ ! -d "$WEB" || ! -f "$WEB/index.php" ]]; then
  echo "ERROR: $WEB belum siap sebagai document root (butuh index.php)."
  echo "Jika project masih utuh di public_html, jalankan sekali:"
  echo "  bash $APP/scripts/cpanel-flatten-to-public-html.sh"
  exit 1
fi

if [[ -f "$WEB/public/index.php" ]]; then
  echo "ERROR: terdeteksi $WEB/public/index.php — layout masih 'project di public_html'."
  echo "Jalankan sekali flatten, bukan sync."
  exit 1
fi

echo "==> Menyalin aset dari $SRC ke $WEB (tanpa index.php)"
# Jangan timpa index.php khusus cPanel (bootstrap ke ../mtsn11majalengka).
if command -v rsync >/dev/null 2>&1; then
  rsync -a --delete \
    --exclude 'index.php' \
    --exclude 'storage' \
    --exclude '.user.ini' \
    "$SRC/" "$WEB/"
else
  # Fallback tanpa rsync: salin folder penting; hapus CSS/JS build lama agar tidak nyangkut.
  rm -rf "$WEB/build"
  cp -a "$SRC/build" "$WEB/build"
  for item in .htaccess favicon.ico robots.txt sw.js css js images fonts vendor; do
    if [[ -e "$SRC/$item" ]]; then
      rm -rf "$WEB/$item"
      cp -a "$SRC/$item" "$WEB/$item"
    fi
  done
fi

echo "==> Memastikan index.php mengarah ke $APP"
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

echo "==> Storage symlink"
rm -rf "$WEB/storage"
ln -sfn "$APP/storage/app/public" "$WEB/storage"
ls -la "$WEB/storage"

echo "==> Memastikan .htaccess rewrite"
bash "$APP/scripts/ensure-public-html-htaccess.sh" || true

echo "==> Clear cache Laravel"
(
  cd "$APP"
  php artisan optimize:clear
)

echo
echo "Selesai. Hard-refresh situs (Ctrl+F5) agar CSS baru termuat."
echo "Cek: https://mtsn11majalengka.sch.id/"
