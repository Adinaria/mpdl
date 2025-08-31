#!/bin/bash

if [ ! -d "vendor" ]; then
  composer install --no-interaction --optimize-autoloader --no-dev
fi
