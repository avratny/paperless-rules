#!/bin/sh

echo "Starting services..."
/usr/sbin/php-fpm8.4 -R --nodaemonize &
nginx &
sleep 5 &

for i in 1 2 3 4 5; do
  cd /var/www/html && php artisan migrate --force && break
  echo "Migrate failed, retrying in 5 seconds..."
  sleep 5
done &

for i in 1 2 3 4 5; do
  cd /var/www/html && php artisan services:start && break
  echo "Queue start failed, retrying in 5 seconds..."
  sleep 5
done &

echo "Ready."
tail -s 1 /var/log/nginx/*.log -f
