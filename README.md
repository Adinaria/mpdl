# Laravel API Project - Инструкция по установке

> Laravel API для управления пользователями и ролями

## 🎯 Что включено в проект

- **Laravel 12** с PHP 8.2
- **RESTful API** для пользователей и ролей
- **Laravel Sanctum** аутентификация  
- **Spatie Permissions** система ролей
- **Unit тесты** (Pest)
- **Docker** контейнеризация

---

## 📋 Требования

- **Docker** и **Docker Compose**
- **Git**

---

## 🛠️ Установка и запуск

### 1️⃣ Подготовка

```bash
git clone https://github.com/Adinaria/mpdl.git
cd mpdl
```

### 2️⃣ Запуск контейнеров

```bash
cd .docker
docker-compose build
docker-compose up -d
```

### 3️⃣ Настройка Laravel

```bash
# Заходим в контейнер
docker exec -it app_test bash

# Ручная настройка
composer install --optimize-autoloader   # Оставляю все зависимости, так как возможно нужно будет локально запустить тесты (сборку для прода отдельную не делал)
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link

# Права доступа
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

exit
```

---

## ✅ Проверка работоспособности

### Веб-приложение
🌐 **http://localhost:8039**

### База данных (Adminer)
🗄️ **http://localhost:6031**
- Сервер: `db`
- Логин: `test`
- Пароль: `test`
- База: `test`

---

## 🧪 Запуск тестов

```bash
# Заходим в контейнер
docker exec -it app_test bash

# Unit тесты
php artisan test tests/Unit/

```

---

## ⚠️ Возможные проблемы и решения

### Порт 8039 занят
Измени в `.docker/docker-compose.yml`:
```yaml
ports:
  - "8040:80"  # вместо 8039:80
```

### Проблемы с правами
```bash
# В контейнере
docker exec -it app_test chown -R www-data:www-data storage bootstrap/cache
```

## 🏗️ Архитектура проекта

```
app/
├── Http/Controllers/API/V1/    # API контроллеры
├── Http/Requests/API/V1/       # Валидация запросов
├── Http/Resources/API/V1/      # API ресурсы
├── Services/                   # Бизнес-логика
└── DTOs/                       # Объекты передачи данных

tests/
└── Unit/                      # Unit тесты

.docker/
├── docker-compose.yml         # Docker конфигурация
├── Dockerfile                # PHP образ
├── nginx/                    # Nginx конфигурация
└── mysql/                    # MySQL данные
```

---

### Горячая перезагрузка
Код автоматически обновляется, перезапуск не нужен.

---

## 📝 Что реализовано

✅ **Sanctum аутентификация**  
✅ **CRUD для пользователей и ролей**  
✅ **Система разрешений (Spatie)**  
✅ **API ресурсы и валидация**  
✅ **Unit тесты**  
✅ **Docker контейнеризация**  
✅ **Кеширование сущностей**  
✅ **Repository + Service паттерны**  

---
