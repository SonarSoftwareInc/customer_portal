#!/bin/bash
su www-data -s /bin/bash <<EOSU
cd /var/www/html
rm -f bootstrap/cache/*

touch storage/database.sqlite
php artisan migrate --force
php artisan cache:clear
php artisan view:clear
php artisan route:cache
php artisan config:cache
EOSU