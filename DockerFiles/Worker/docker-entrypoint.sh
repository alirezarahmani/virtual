#!/bin/bash
set -e
eval $(stat -c 'usermod -u %u -g %g www-data' /var/www) || true
/etc/init.d/php8.0-fpm start
exec "$@"
