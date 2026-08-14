#!/usr/bin/env bash
# Pulihkan public/.htaccess dari Git setelah cPanel MultiPHP menimpanya.
# Jalankan dari root project Laravel di hosting:
#   bash scripts/restore-htaccess.sh

set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

if [[ ! -f public/.htaccess ]]; then
  echo "Tidak menemukan public/.htaccess"
  exit 1
fi

git checkout -- public/.htaccess
echo "public/.htaccess dipulihkan dari Git."
echo "Cek blok PHP handler (ea-php83) masih sesuai MultiPHP Manager."
