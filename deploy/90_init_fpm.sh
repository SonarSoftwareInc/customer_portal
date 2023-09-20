#!/bin/bash
set -euf -o pipefail

mkdir -p /run/php
touch /run/php/php8.2-fpm.sock
chown www-data:www-data /run/php/php8.2-fpm.sock
