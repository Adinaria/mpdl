#!/bin/bash

echo "🚀 Запуск автоматической настройки Laravel..."

# Установка Composer зависимостей
echo "📦 Установка Composer зависимостей..."
composer install --optimize-autoloader

# Копирование .env файла
echo "⚙️ Настройка окружения..."
if [ ! -f ".env" ]; then
    cp .env.example .env
    echo "✅ Файл .env создан"
else
    echo "ℹ️ Файл .env уже существует"
fi

# Генерация ключа приложения
echo "🔑 Генерация ключа приложения..."
php artisan key:generate

# Выполнение миграций
echo "🗄️ Выполнение миграций базы данных..."
php artisan migrate:fresh

# Заполнение базы данных
echo "🌱 Заполнение базы данных тестовыми данными..."
php artisan db:seed

# Создание символической ссылки для storage
echo "🔗 Создание символической ссылки для storage..."
php artisan storage:link

# Создание API документации
echo "🔗 Создание API документации"
php artisan scribe:generate

# Настройка прав доступа
echo "🔒 Настройка прав доступа..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache


# Очистка и кеширование конфигурации
echo "🧹 Оптимизация приложения..."
php artisan config:cache
php artisan route:cache

echo "🎉 Настройка Laravel завершена успешно!"
echo "🌐 Приложение доступно по адресу: http://localhost:8039"
echo "🗄️ Adminer доступен по адресу: http://localhost:6031"
