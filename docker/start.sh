#!/bin/sh

echo "Starting services..."
/usr/sbin/php-fpm8.4 -R --nodaemonize &
nginx &
cd /var/www/html && php artisan services:start &
echo "Ready."
tail -s 1 /var/log/nginx/*.log -f
