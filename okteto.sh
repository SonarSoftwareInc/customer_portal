#!/bin/bash
set -euf -o pipefail

mkdir -p /var/www/html/storage/framework/{cache,views}

sudo mkdir -p /var/www/.cache/
sudo mkdir -p /var/www/.config/
sudo chown www-data /var/www/.cache/
sudo chown www-data /var/www/.config/

composer install

sudo -E /sbin/my_init --no-kill-all-on-exit --skip-runit true

# Clear out the Laravel log
touch /var/www/html/storage/logs/laravel.log
echo '' > /var/www/html/storage/logs/laravel.log

sudo -E pkill php-fpm || true
sudo -E /usr/sbin/php-fpm8.2

sudo -E pkill nginx || true
sudo -E /usr/sbin/nginx

exec bash
