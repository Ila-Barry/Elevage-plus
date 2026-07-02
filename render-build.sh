#!/usr/bin/env bash
set -o errexit

# On force la suppression d'un éventuel lien cassé avant de le recréer
rm -rf public/storage

# On installe proprement et on recrée le lien
composer install --no-dev --optimize-autoloader
php artisan storage:link