#!/bin/bash
set -e
eval $(stat -c 'usermod -u %u -g %g www-data' /var/www) || true
/etc/init.d/php7.4-fpm start
cd /var/www/ && php artisan migrate
cd /var/www/ && php artisan cache:clear
exec "$@"
