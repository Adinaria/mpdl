#!/bin/bash

if [ ! -d "vendor" ]; then
  composer install --no-interaction --optimize-autoloader --no-dev
fi

RUN cp .env.example .env
RUN var/project/php artisan key:generate
