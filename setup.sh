#!/bin/bash
set -e
mkdir -p storage/framework/{views,cache/data,sessions,testing} storage/logs storage/app/public bootstrap/cache
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
composer install
if [ ! -f .env ]; then
  cp .env.example .env
  php artisan key:generate
fi
echo "Done. Configure DB in .env then run: php artisan migrate && php artisan serve"
