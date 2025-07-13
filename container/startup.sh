#!/bin/sh

php /var/www/html/artisan migrate --quiet
php /var/www/html/artisan octane:start --server=frankenphp
