# Решение ошибки 502 Bad Gateway

## Что означает ошибка 502?

Ошибка 502 Bad Gateway означает, что приложение не запускается или падает при старте. Это критическая ошибка, которая требует немедленной диагностики.

## Пошаговая диагностика

### Шаг 1: Включите отладку (ОБЯЗАТЕЛЬНО)

В Railway → Variables добавьте:

```
APP_DEBUG=true
APP_ENV=local
```

Это покажет детальную ошибку вместо 502.

### Шаг 2: Проверьте логи деплоя

1. В Railway откройте **Deployments**
2. Выберите последний деплой
3. Откройте **View Logs**
4. Ищите ошибки:
   - `Fatal error`
   - `Class 'Laravel\Nova\...' not found`
   - `No application encryption key`
   - Ошибки подключения к БД

### Шаг 3: Проверьте переменные окружения

В Railway → Variables убедитесь, что установлены:

**Обязательные:**
- `APP_KEY` - должен быть сгенерирован (начинается с `base64:`)
- `APP_ENV=production` (или `local` для отладки)
- `APP_DEBUG=false` (или `true` для отладки)

**Для базы данных:**
- `DB_CONNECTION=mysql`
- `DB_HOST` (или `MYSQL_HOST`)
- `DB_DATABASE` (или `MYSQL_DATABASE`)
- `DB_USERNAME` (или `MYSQL_USER`)
- `DB_PASSWORD` (или `MYSQL_PASSWORD`)

**Для Nova:**
- `COMPOSER_AUTH` (если используете Вариант 1)

### Шаг 4: Проверьте через Railway Shell

Откройте Railway Shell и выполните:

```bash
# Проверьте логи приложения
tail -n 100 storage/logs/laravel.log

# Проверьте, что Nova установлена
composer show laravel/nova

# Проверьте подключение к БД
php artisan tinker
# В tinker: DB::connection()->getPdo();

# Очистите все кеши
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
```

### Шаг 5: Проверьте права доступа

```bash
chmod -R 775 storage bootstrap/cache
```

## Частые причины ошибки 502:

### 1. Отсутствует APP_KEY

**Симптом:** `No application encryption key has been specified`

**Решение:**
1. В Railway Shell выполните:
   ```bash
   php artisan key:generate --show
   ```
2. Скопируйте ключ (начинается с `base64:`)
3. В Railway → Variables добавьте:
   - **Key:** `APP_KEY`
   - **Value:** скопированный ключ

### 2. Nova не установлена, но пытается загрузиться

**Симптом:** `Class 'Laravel\Nova\...' not found`

**Решение:**
Проверьте `bootstrap/providers.php` - там должна быть проверка на существование Nova:

```php
if (class_exists(\Laravel\Nova\Nova::class)) {
    $providers[] = App\Providers\NovaServiceProvider::class;
}
```

### 3. Проблема с базой данных

**Симптом:** Ошибка подключения к БД

**Решение:**
1. Убедитесь, что MySQL сервис запущен в Railway
2. Проверьте переменные окружения для БД
3. Выполните миграции:
   ```bash
   php artisan migrate --force
   ```

### 4. Приложение не запускается

**Симптом:** Нет ошибок в логах, но 502

**Решение:**
1. Проверьте команду запуска в Railway Settings
2. Убедитесь, что используется правильный порт (Railway автоматически устанавливает `PORT`)
3. Проверьте, что веб-сервер запущен

### 5. Проблема с зависимостями

**Симптом:** Ошибки при `composer install`

**Решение:**
1. Проверьте логи деплоя - должен быть успешный `composer install`
2. Если есть ошибки с Nova, проверьте `COMPOSER_AUTH`

## Быстрое решение (по порядку):

1. **Включите отладку:**
   ```
   APP_DEBUG=true
   APP_ENV=local
   ```

2. **Проверьте APP_KEY:**
   - Если нет, сгенерируйте через Shell: `php artisan key:generate --show`
   - Добавьте в Railway Variables

3. **Проверьте логи деплоя** - найдите конкретную ошибку

4. **Очистите кеши через Shell:**
   ```bash
   php artisan optimize:clear
   ```

5. **Проверьте базу данных:**
   ```bash
   php artisan migrate:status
   ```

## Если ничего не помогает:

Выполните полную переустановку:

```bash
# В Railway Shell
rm -rf vendor bootstrap/cache/*.php storage/framework/cache/*
composer install --no-interaction --optimize-autoloader --ignore-platform-req=ext-zip
php artisan config:clear
php artisan cache:clear
php artisan optimize:clear
```

## Важно:

После исправления ошибки:
1. Верните `APP_DEBUG=false` для production
2. Установите `APP_ENV=production`
3. Проверьте, что приложение работает

