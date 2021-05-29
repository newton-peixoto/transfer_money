#!/bin/sh

cd /var/www/

cp ./src/.env.example ./src/.env

cd src/

composer install

php artisan config:clear
php artisan migrate

sudo chown -R lumen:lumen /var/www

set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
        set -- php-fpm "$@"
fi

exec "$@"