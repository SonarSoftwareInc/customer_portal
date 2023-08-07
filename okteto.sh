#!/bin/bash
set -euf -o pipefail

mkdir -p /var/www/html/storage/framework/{cache,views}

composer install

php /var/www/html/artisan config:clear
php /var/www/html/artisan route:clear

sudo -E /sbin/my_init --no-kill-all-on-exit --skip-runit true

# Clear out the Laravel log
touch /var/www/html/storage/logs/laravel.log
echo '' > /var/www/html/storage/logs/laravel.log

sudo -E pkill php-fpm || true
sudo -E /usr/sbin/php-fpm7.3

sudo -E pkill nginx || true
sudo -E /usr/sbin/nginx

exec bash
