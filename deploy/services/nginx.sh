#!/bin/sh
sv start php-fpm
exec /usr/sbin/nginx -g "daemon off;"
