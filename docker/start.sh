#!/bin/sh

echo "Starting services..."
/usr/sbin/php-fpm8.4 -R --nodaemonize &
nginx &

for i in 1 2 3 4 5; do
  cd /var/www/html && php artisan services:start && break
  echo "Queue start failed, retrying in 5 seconds..."
  sleep 5
done &

echo "Ready."
tail -s 1 /var/log/nginx/*.log -f
