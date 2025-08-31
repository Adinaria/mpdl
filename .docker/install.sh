#!/bin/bash

if [ ! -d "vendor" ]; then
  composer install --no-interaction --optimize-autoloader --no-dev
fi

# Копируем .env
if [ ! -f ".env" ]; then
    cp .env.example .env
fi

# Генерируем ключ приложения
php artisan key:generate
