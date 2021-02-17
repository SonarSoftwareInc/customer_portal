#!/bin/bash
set -euf -o pipefail

composer install

sudo -E /sbin/my_init --no-kill-all-on-exit --skip-runit true &

php /var/www/html/artisan config:clear
php /var/www/html/artisan route:clear

# Clear out the Laravel log
touch /var/www/html/storage/logs/laravel.log
echo '' > /var/www/html/storage/logs/laravel.log

sudo pkill php-fpm || true
sudo /usr/sbin/php-fpm7.3

sudo pkill nginx || true
sudo /usr/sbin/nginx

exec bash
