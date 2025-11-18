# Laravel Backend с Nova и Sanctum для SPA

Этот репозиторий содержит бекенд на Laravel 11 с интеграцией Laravel Nova и Laravel Sanctum, настроенный для работы как API для Single Page Application (SPA) без CSRF токенов.

## 🚀 Особенности

- **Laravel 11** - последняя версия фреймворка
- **Laravel Nova** - административная панель
- **Laravel Sanctum** - аутентификация для SPA (без CSRF)
- **Railway Ready** - готов к деплою на Railway
- **CORS настроен** - поддержка кросс-доменных запросов для SPA

## 📋 Требования

- PHP >= 8.2
- Composer
- MySQL/PostgreSQL/SQLite
- Node.js и NPM (для фронтенда, если нужно)

## 🔧 Установка

1. **Клонируйте репозиторий:**
```bash
git clone <your-repo-url>
cd PRFRONT
```

2. **Установите зависимости:**
```bash
composer install
```

3. **Настройте файл окружения:**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Настройте базу данных в `.env`:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

5. **Настройте Sanctum для SPA в `.env`:**
```env
SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1,localhost:3000,localhost:5173
SESSION_DOMAIN=localhost
```

6. **Запустите миграции:**
```bash
php artisan migrate
```

7. **Установите Nova:**
```bash
php artisan nova:install
```

После установки добавьте лицензионный ключ Nova в `.env`:
```env
NOVA_LICENSE_KEY=your-nova-license-key
```

## 🔐 Настройка Sanctum для SPA

Проект настроен для работы с SPA без CSRF токенов:

- **API маршруты** (`/api/*`) не требуют CSRF защиты
- **CORS настроен** для работы с фронтендом
- **Stateful домены** указаны в `SANCTUM_STATEFUL_DOMAINS`

### Пример использования в фронтенде:

```javascript
// Инициализация (получение CSRF cookie)
await axios.get('http://localhost:8000/sanctum/csrf-cookie', {
  withCredentials: true
})

// Логин
await axios.post('http://localhost:8000/login', {
  email: 'user@example.com',
  password: 'password'
}, {
  withCredentials: true
})

// Защищенные API запросы
await axios.get('http://localhost:8000/api/user', {
  withCredentials: true
})
```

**Важно:** Для работы Sanctum в SPA режиме все запросы должны включать `withCredentials: true`.

## 🚂 Деплой на Railway

### Подготовка к деплою:

1. **Создайте аккаунт на [Railway](https://railway.app)**

2. **Подключите репозиторий:**
   - В Railway создайте новый проект
   - Выберите "Deploy from GitHub repo"
   - Подключите этот репозиторий

3. **Настройте переменные окружения в Railway:**
   
   Обязательные переменные:
   ```
   APP_KEY=base64:... (сгенерируйте: php artisan key:generate --show)
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-app.railway.app
   
   DB_CONNECTION=mysql
   DB_HOST=${{MySQL.MYSQLHOST}}
   DB_PORT=${{MySQL.MYSQLPORT}}
   DB_DATABASE=${{MySQL.MYSQLDATABASE}}
   DB_USERNAME=${{MySQL.MYSQLUSER}}
   DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}
   
   SANCTUM_STATEFUL_DOMAINS=your-frontend-domain.com,your-railway-app.railway.app
   SESSION_DOMAIN=.railway.app
   
   NOVA_LICENSE_KEY=your-nova-license-key
   ```

4. **Добавьте MySQL сервис:**
   - В Railway добавьте MySQL сервис
   - Railway автоматически предоставит переменные окружения `${{MySQL.*}}`

5. **Настройте команды запуска:**
   
   Railway автоматически использует `Procfile` или `railway.json`:
   - **Procfile**: `web: php artisan serve --host=0.0.0.0 --port=$PORT`
   - Или через `railway.json` с автоматической сборкой

### Автоматический деплой:

Railway автоматически:
1. Определит PHP проект через `composer.json`
2. Выполнит `composer install --no-dev`
3. Запустит миграции (если настроено)
4. Запустит приложение через `Procfile`

### Выполнение миграций на Railway:

В настройках сервиса Railway добавьте команду сборки:
```bash
composer install --no-dev --optimize-autoloader && php artisan nova:install --no-interaction && php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache
```

Или через Railway CLI:
```bash
railway run php artisan migrate --force
```

## 📁 Структура проекта

```
PRFRONT/
├── app/                    # Код приложения
│   ├── Http/
│   │   └── Middleware/     # Middleware для CORS и Sanctum
│   └── Providers/          # Service Providers
├── bootstrap/
│   └── app.php            # Конфигурация Laravel 11 (Sanctum без CSRF)
├── config/                # Конфигурационные файлы
│   ├── sanctum.php        # Настройки Sanctum для SPA
│   ├── cors.php           # CORS конфигурация
│   └── session.php        # Настройки сессий
├── routes/
│   ├── api.php            # API маршруты
│   └── web.php            # Web маршруты
├── railway.json           # Конфигурация Railway
├── Procfile              # Команда запуска для Railway
├── nixpacks.toml         # Альтернативная конфигурация деплоя
└── composer.json         # Зависимости PHP
```

## 🔍 Важные замечания

### CSRF защита отключена для API:
- API маршруты (`/api/*`) не требуют CSRF токенов
- Web маршруты защищены CSRF по умолчанию
- Sanctum работает в SPA режиме с cookie-based аутентификацией

### CORS настройки:
- Измените `CORS_ALLOWED_ORIGINS` в `.env` для вашего фронтенда
- Или настройте `allowed_origins` в `config/cors.php`

### HTTPS на Railway:
- В `AppServiceProvider` автоматически включается HTTPS в production
- Railway автоматически предоставляет SSL сертификаты

## 🧪 Тестирование

```bash
php artisan test
```

## 📝 Лицензия

MIT

