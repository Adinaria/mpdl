# Laravel API Project - Инструкция по установке

> **Это тестовое задание** с Laravel API для управления пользователями и ролями

## 🎯 Что включено в проект

- **Laravel 12** с PHP 8.2
- **RESTful API** для пользователей и ролей
- **Laravel Sanctum** аутентификация  
- **Spatie Permissions** система ролей
- **Unit & Integration тесты** (Pest)
- **Docker** контейнеризация

---

## 📋 Требования

- **Docker** и **Docker Compose**
- **Git**
- Минимум **2GB RAM**
- Минимум **5GB** свободного места

---

## 🚀 Быстрый старт

### 1️⃣ Клонирование и подготовка

```bash
# Клонируем проект
git clone <repository-url>
cd mpdl

# Создаем Docker сеть (ОБЯЗАТЕЛЬНО!)
docker network create project-network
```

### 2️⃣ Настройка окружения

```bash
# Копируем конфигурацию
cp data/project/.env.example data/project/.env

# Файл .env уже настроен для Docker, менять ничего не нужно!
```

### 3️⃣ Запуск Docker контейнеров

```bash
# Собираем образы
docker-compose build

# Запускаем все контейнеры
docker-compose up -d

# Проверяем что все запустилось
docker-compose ps
```

Должны быть запущены:
- `app_test` (PHP приложение)
- `webserver_test` (Nginx)  
- `db_test` (MariaDB)
- `adminer_test` (Web DB интерфейс)

### 4️⃣ Настройка Laravel

```bash
# Заходим в контейнер приложения
docker exec -it app_test bash

# Выполняем команды ВНУТРИ контейнера:
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link

# Настройка прав доступа
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Выходим из контейнера
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

### API тестирование
📡 Используй **Postman** или **curl**:

```bash
# Регистрация пользователя
curl -X POST http://localhost:8039/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test",
    "last_name": "User", 
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'

# Получишь токен в ответе, используй его дальше
```

---

## 🧪 Запуск тестов

```bash
# Заходим в контейнер
docker exec -it app_test bash

# Все тесты
php artisan test

# Unit тесты
php artisan test tests/Unit/

# Feature тесты  
php artisan test tests/Feature/

# Pest тесты
./vendor/bin/pest
```

---

## 📚 API Documentation

### 🔐 Аутентификация

| Метод | URL | Описание |
|-------|-----|----------|
| POST | `/api/v1/auth/register` | Регистрация |
| POST | `/api/v1/auth/login` | Вход |
| POST | `/api/v1/auth/logout` | Выход (с токеном) |

### 👥 Пользователи (требует токен)

| Метод | URL | Описание |
|-------|-----|----------|
| GET | `/api/v1/users` | Список пользователей |
| GET | `/api/v1/users/{uuid}` | Получить пользователя |
| PUT | `/api/v1/users/{uuid}` | Обновить пользователя |

### 🛡️ Роли (требует токен)

| Метод | URL | Описание |
|-------|-----|----------|
| GET | `/api/v1/roles` | Список ролей |
| GET | `/api/v1/roles/{uuid}` | Получить роль |
| PUT | `/api/v1/roles/{uuid}` | Обновить роль |
| DELETE | `/api/v1/roles/{uuid}` | Удалить роль |

---

## 🔧 Полезные команды

### Docker управление
```bash
# Просмотр логов
docker-compose logs -f

# Остановка
docker-compose down

# Перезапуск конкретного контейнера
docker-compose restart app
```

### Laravel команды
```bash
# Очистка кешей
php artisan optimize:clear

# Просмотр маршрутов
php artisan route:list

# Миграции заново
php artisan migrate:fresh --seed
```

---

## ⚠️ Возможные проблемы и решения

### "network project-network not found"
```bash
docker network create project-network
```

### Порт 8039 занят
Измени в `docker-compose.yml`:
```yaml
ports:
  - "8040:80"  # вместо 8039:80
```

### Проблемы с правами
```bash
# На хосте
sudo chown -R $USER:$USER data/project/

# В контейнере
docker exec -it app_test chown -R www-data:www-data storage
```

### Контейнер app не запускается
```bash
# Проверь логи
docker-compose logs app

# Пересобери образ
docker-compose build --no-cache app
```

---

## 🏗️ Архитектура проекта

```
app/
├── Http/Controllers/API/V1/    # API контроллеры
├── Http/Requests/API/V1/       # Валидация запросов
├── Http/Resources/API/V1/      # API ресурсы
├── Services/                   # Бизнес-логика
├── DTOs/                      # Объекты передачи данных
└── Models/                    # Eloquent модели

tests/
├── Unit/                      # Unit тесты
└── Feature/                   # Integration тесты
```

---

## 🎭 Для разработки

### Включить debug режим
```bash
# В .env измени:
APP_ENV=local
APP_DEBUG=true

# Очисти кеш
php artisan config:clear
```

### Горячая перезагрузка
Код автоматически обновляется через Docker volumes, перезапуск не нужен.

---

## 📝 Что реализовано

✅ **JWT/Sanctum аутентификация**
✅ **CRUD для пользователей и ролей**  
✅ **Система разрешений (Spatie)**
✅ **API ресурсы и валидация**
✅ **Unit и Integration тесты**
✅ **Docker контейнеризация**
✅ **Кеширование сущностей**
✅ **Repository + Service паттерны**

---

*Проект готов к тестированию! 🚀*
