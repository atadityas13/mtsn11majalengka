#!/usr/bin/env bash
# Jadikan folder public/ sebagai document root cPanel (andalan).
# Jalankan di Terminal cPanel / SSH sebagai user hosting:
#   bash scripts/cpanel-use-public-docroot.sh
#
# Prasyarat: project Laravel saat ini ada di ~/public_html
# (terlihat folder app, vendor, public, artisan).

set -euo pipefail

HOME_DIR="${HOME:-}"
if [[ -z "$HOME_DIR" ]]; then
  echo "HOME tidak ditemukan."
  exit 1
fi

SRC="$HOME_DIR/public_html"
APP="$HOME_DIR/mtsn11majalengka"
LINK="$HOME_DIR/public_html"

if [[ ! -d "$SRC/public" || ! -f "$SRC/public/index.php" ]]; then
  echo "Tidak menemukan $SRC/public/index.php"
  echo "Sesuaikan path di script ini jika project bukan di ~/public_html"
  exit 1
fi

if [[ -L "$SRC" ]]; then
  echo "public_html sudah symlink:"
  ls -la "$SRC"
  echo "Selesai — tidak perlu diubah."
  exit 0
fi

if [[ -e "$APP" ]]; then
  echo "Folder $APP sudah ada. Hentikan agar tidak menimpa data."
  exit 1
fi

echo "1) Memindahkan project: public_html -> mtsn11majalengka"
mv "$SRC" "$APP"

echo "2) Symlink: public_html -> mtsn11majalengka/public"
ln -s "$APP/public" "$LINK"

echo "3) Hasil:"
ls -la "$LINK"
echo
echo "Cek browser: https://mtsn11majalengka.sch.id/"
echo "Harus tampil situs, bukan daftar folder."
echo
echo "Catatan: MultiPHP boleh menambah blok handler di public/.htaccess — itu normal."
