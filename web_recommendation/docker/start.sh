#!/usr/bin/env sh
set -eu

export PORT="${PORT:-8080}"
export DB_CONNECTION="${DB_CONNECTION:-sqlite}"

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache database

if [ "$DB_CONNECTION" = "sqlite" ]; then
    export DB_DATABASE="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
    touch "$DB_DATABASE"
fi

chown -R www-data:www-data storage bootstrap/cache database

envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/http.d/default.conf

php artisan storage:link || true
php artisan config:clear
php artisan cache:clear || true
php artisan migrate --force
php artisan db:seed --class=PlaceSeeder --force
php artisan config:cache
php artisan route:cache || true
php artisan view:cache || true

php-fpm -D
exec nginx -g 'daemon off;'
