#!/usr/bin/env bash
# exit on error
set -o errexit

composer install --no-dev --optimize-autoloader
php artisan storage:link
mkdir -p storage/app/public/uploads/publications/images