# Laravel API Project - Инструкция

> Laravel API для управления пользователями и ролями

## 🎯 Что включено в проект

- **Laravel 12** с PHP 8.2
- **RESTful API** для пользователей и ролей
- **Laravel Sanctum** аутентификация
- **Spatie Permissions** система ролей
- **Spatie Laravel data** Data transfer Objects (DTO)
- **Unit тесты** (Pest)
- **API документация** (Scribe)
- **Docker** контейнеризация

---

## Документация

- **Документация доступна по ссылке {domain}/docs**

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

### 3️⃣ Автоматическая настройка Laravel

```bash
# Заходим в контейнер и запускаем автоматическую настройку
docker exec -it app_test bash -c "chmod +x .docker/setup.sh && ./.docker/setup.sh"
```

Скрипт автоматически выполнит:

- ✅ Установку Composer зависимостей
- ✅ Копирование .env файла
- ✅ Генерацию ключа приложения
- ✅ Миграции базы данных
- ✅ Заполнение тестовыми данными
- ✅ Создание символических ссылок
- ✅ Настройку прав доступа
- ✅ Создание API документации (Scribe)
- ✅ Оптимизацию приложения (кеширование)

---

## 🛠️ Ручная установка (если автоматическая не сработала)

### 1️⃣ Подготовка

```bash
git clone https://github.com/Adinaria/mpdl.git
cd mpdl
docker network create project-network
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
composer install --optimize-autoloader
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link
php artisan scribe:generate

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

### API документация 

🗄️ **http://localhost:8039/docs**

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
✅ **API документация**
✅ **Unit тесты**  
✅ **Docker контейнеризация**  
✅ **Кеширование сущностей**  
✅ **Repository + Service паттерны**  
❌ **Нагрузочное тестирование**

## 📝 Архитектурные решения

1) Использовал паттерны Repository и Service для разделения логики доступа к данным и бизнес-логики. Это улучшает тестируемость и поддержку кода.
2) Применил DTO (Data Transfer Objects) для четкой структуры данных, передаваемых между слоями приложения. Это снижает связность и упрощает валидацию.
3) Внедрил кеширование запрашиваемых сущностей (пользователи, роли). Кеширование обернул оберткой из трейта, чтобы можно было легко управлять кешируемыми сущностями.
Инвалидация кеша присходит в boot модели, так как по моей логики мы всегда проходим через boot модели.
Записываю даже пустые значения в кеш, чтобы избежать лишних запросов к базе, данные наложиться друг на друга не могут, так как при CRUD действиях происходит очистка.
4) Использовал Spatie Permission пакет, так как в нем сразу реализованы роли, которые упростили работу.
5) Из оптимизации было сделано: кеширования конфигурации и маршрутов, оптимизация автозагрузки классов (singleton). В бд были добавлены индексы на часто запрашиваемые поля (uuid).
Добавлены select field для нужных полей в получаемых списках (ролей, пользователей).
6) Добавлено мягкое удаление записей, для возможности восстановления данных.

---

## 📝 Дополнительная информация
1) Нагрузочное тестирование не сделано, так как потребовалось много времени на этот процесс.
Планировалось подключить prometheus + Graphana Cloud и нагружать систему через k6. Попытки это сделать есть в ветке prometheus
Там реализовались контейнр prometheus и exporters для nginx, php-fpm (до mysql не дошел).
2) Время реализация на все задачи ушло порядка 16 часов (беру приблизительное время)
3) Проект без фронта

