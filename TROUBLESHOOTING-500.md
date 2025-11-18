# Решение ошибки 500

## Диагностика ошибки 500

Если вы получаете ошибку 500 на главной странице, но `/up` работает, выполните следующие шаги:

### 1. Проверьте логи в Railway

В Railway откройте:
- **Deployments** → выберите последний деплой → **View Logs**
- Или **Metrics** → **Logs**

Ищите ошибки типа:
- `Class 'Laravel\Nova\...' not found`
- `Fatal error`
- `Uncaught exception`

### 2. Проверьте через Railway Shell

Откройте Railway Shell и выполните:

```bash
# Проверьте логи приложения
tail -n 50 storage/logs/laravel.log

# Или если логов нет, включите отладку временно
# В Railway Variables установите:
# APP_DEBUG=true
# APP_ENV=local
```

### 3. Очистите все кеши

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
```

### 4. Проверьте права доступа

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 5. Проверьте переменные окружения

Убедитесь, что установлены:
- `APP_KEY` - должен быть сгенерирован
- `APP_ENV=production` (или `local` для отладки)
- `APP_DEBUG=false` (или `true` для отладки)

### 6. Временное включение отладки

Для диагностики временно установите в Railway Variables:
```
APP_DEBUG=true
APP_ENV=local
```

Это покажет детальную ошибку вместо 500.

### 7. Проверьте базу данных

```bash
php artisan migrate:status
php artisan db:show
```

## Частые причины ошибки 500:

### 1. Проблема с Nova (если не установлена)

**Симптом:** Ошибка `Class 'Laravel\Nova\...' not found`

**Решение:** 
- Убедитесь, что Nova удалена из `composer.json`
- Проверьте `bootstrap/providers.php` - NovaServiceProvider не должен загружаться

### 2. Проблема с базой данных

**Симптом:** Ошибка подключения к БД

**Решение:**
- Проверьте переменные окружения для БД
- Убедитесь, что БД создана в Railway

### 3. Проблема с правами доступа

**Симптом:** Ошибки записи в `storage/` или `bootstrap/cache/`

**Решение:**
```bash
chmod -R 775 storage bootstrap/cache
```

### 4. Проблема с конфигурацией

**Симптом:** Ошибки при загрузке конфигурации

**Решение:**
```bash
php artisan config:clear
php artisan config:cache
```

## Быстрое решение:

1. В Railway Variables установите `APP_DEBUG=true`
2. Обновите страницу - увидите детальную ошибку
3. Исправьте проблему
4. Верните `APP_DEBUG=false`

## Если ничего не помогает:

Выполните полную переустановку зависимостей:

```bash
rm -rf vendor composer.lock
composer install --no-interaction --optimize-autoloader --ignore-platform-req=ext-zip
php artisan config:clear
php artisan cache:clear
```

