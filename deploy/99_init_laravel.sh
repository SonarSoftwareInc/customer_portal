#!/bin/bash
set -euf -o pipefail

chown -R www-data:www-data /var/www/html

su www-data -s /bin/bash <<EOSU
cd /var/www/html
rm -f bootstrap/cache/*

touch storage/database.sqlite
php artisan migrate --force
php artisan cache:clear
php artisan view:clear
php artisan config:cache

if [ $DEV != true ]; then
  php artisan route:cache
fi

EOSU