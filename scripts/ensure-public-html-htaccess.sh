#!/usr/bin/env bash
# Pulihkan rewrite Laravel di ~/public_html/.htaccess
# jika MultiPHP menimpa file jadi hanya handler PHP.
#
# Crontab (disarankan):
#   */5 * * * * bash ~/mtsn11majalengka/scripts/ensure-public-html-htaccess.sh >/dev/null 2>&1

set -euo pipefail

WEB="${HOME:?}/public_html"
FILE="$WEB/.htaccess"

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
  echo "Tidak ada $WEB"
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
  # Sudah ada rewrite — tidak diubah
  exit 0
fi

printf '%s\n\n%s\n' "$LARAVEL_RULES" "$HANDLER" > "$FILE"
echo "$(date -Is) restored Laravel rewrite in $FILE"
