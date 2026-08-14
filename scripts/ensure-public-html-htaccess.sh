#!/usr/bin/env bash
# Pulihkan rewrite Laravel di ~/public_html/.htaccess
# jika MultiPHP menimpa file jadi hanya handler PHP.
#
# MultiPHP biasanya menimpa .htaccess HANYA saat ganti versi PHP /
# pengaturan PHP — bukan setiap menit. Jadi cron jarang saja cukup.
#
# Crontab disarankan (1x sehari jam 03:15):
#   15 3 * * * /bin/bash $HOME/mtsn11majalengka/scripts/ensure-public-html-htaccess.sh
#
# Atau lewat cPanel → Cron Jobs (lihat README singkat di komentar bawah).

set -euo pipefail

WEB="${HOME:?}/public_html"
FILE="$WEB/.htaccess"
LOG="${HOME:?}/mtsn11majalengka/storage/logs/htaccess-ensure.log"
APP="${HOME:?}/mtsn11majalengka"

mkdir -p "$(dirname "$LOG")"

log() {
  echo "$(date '+%Y-%m-%d %H:%M:%S') $*" | tee -a "$LOG"
}

LARAVEL_RULES='DirectoryIndex index.php

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
'

DEFAULT_HANDLER='# php -- BEGIN cPanel-generated handler, do not edit
# Set the “ea-php83” package as the default “PHP” programming language.
<IfModule mime_module>
  AddHandler application/x-httpd-ea-php83 .php .php8 .phtml
</IfModule>
# php -- END cPanel-generated handler, do not edit
'

if [[ ! -d "$WEB" ]]; then
  log "ERROR: tidak ada $WEB"
  exit 1
fi

HANDLER=""
if [[ -f "$FILE" ]] && grep -q 'BEGIN cPanel-generated handler' "$FILE"; then
  HANDLER="$(awk '/# php -- BEGIN cPanel-generated handler/,/# php -- END cPanel-generated handler/' "$FILE")"
fi

if [[ -z "$HANDLER" ]]; then
  HANDLER="$DEFAULT_HANDLER"
fi

if [[ -f "$FILE" ]] && grep -q 'RewriteRule \^ index.php' "$FILE"; then
  log "OK: rewrite sudah ada, tidak diubah"
  exit 0
fi

printf '%s\n\n%s\n' "$LARAVEL_RULES" "$HANDLER" > "$FILE"
log "RESTORED: rewrite Laravel dipasang ulang di $FILE"
