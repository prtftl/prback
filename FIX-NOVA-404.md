# Решение ошибки 404 для Nova

## Проблема

Nova возвращает 404 NOT FOUND по адресу `/nova`.

## Возможные причины

### 1. Nova не установлена на сервере

Nova может быть не установлена, если:
- `composer install` не выполнился успешно
- Переменная `COMPOSER_AUTH` не установлена
- Ошибка при установке Nova

### 2. Маршруты Nova не зарегистрированы

Маршруты могут не зарегистрироваться, если:
- `NovaServiceProvider` не загружается
- Nova не обнаружена при загрузке провайдеров

### 3. Проблема с кешем

Кеш конфигурации или маршрутов может быть устаревшим.

## Диагностика

### Шаг 1: Проверьте, установлена ли Nova

В Railway Shell выполните:

```bash
composer show laravel/nova
```

**Ожидаемый результат:**
```
name     : laravel/nova
descrip. : ...
versions : * 5.7.6
```

**Если ошибка "Package laravel/nova not found":**
- Nova не установлена
- Нужно установить через `composer require laravel/nova:5.7.6`

### Шаг 2: Проверьте маршруты Nova

В Railway Shell выполните:

```bash
php artisan route:list | grep nova
```

**Ожидаемый результат:**
Должны быть маршруты, начинающиеся с `/nova`:
```
GET|HEAD  nova .................... nova.login
POST      nova/login .............. nova.login
...
```

**Если маршрутов нет:**
- Маршруты Nova не зарегистрированы
- Проблема с `NovaServiceProvider`

### Шаг 3: Проверьте, загружается ли NovaServiceProvider

В Railway Shell выполните:

```bash
php artisan tinker
```

В tinker:
```php
class_exists(\Laravel\Nova\Nova::class);
// Должно вернуть true

class_exists(\App\Providers\NovaServiceProvider::class);
// Должно вернуть true
```

## Решение

### Решение 1: Установить Nova (если не установлена)

Если Nova не установлена, выполните в Railway Shell:

```bash
# Проверьте, что COMPOSER_AUTH установлен
echo $COMPOSER_AUTH

# Если нет, создайте auth.json
php scripts/setup-auth.php

# Установите Nova
composer require laravel/nova:5.7.6 --no-interaction

# Опубликуйте ресурсы Nova
php artisan nova:install
```

### Решение 2: Очистить кеш

В Railway Shell выполните:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
```

### Решение 3: Проверить переменные окружения

В Railway Variables убедитесь, что установлены:

**Для установки Nova:**
- `COMPOSER_AUTH` (если используете Вариант 1)
- Или `COMPOSER_AUTH_NOVA_USERNAME` + `COMPOSER_AUTH_NOVA_PASSWORD` (если используете Вариант 2)

**Для работы Nova:**
- `NOVA_LICENSE_KEY` (опционально, но рекомендуется для production)

### Решение 4: Переустановить Nova

Если ничего не помогает, переустановите Nova:

```bash
# Удалите Nova
composer remove laravel/nova

# Очистите кеш
php artisan config:clear
php artisan cache:clear

# Установите заново
composer require laravel/nova:5.7.6 --no-interaction

# Опубликуйте ресурсы
php artisan nova:install

# Очистите кеш снова
php artisan optimize:clear
```

### Решение 5: Проверить логи

Проверьте логи приложения на наличие ошибок:

```bash
tail -n 100 storage/logs/laravel.log
```

Ищите ошибки типа:
- `Class 'Laravel\Nova\...' not found`
- Ошибки при загрузке провайдеров
- Ошибки регистрации маршрутов

## Пошаговая инструкция

### 1. Проверьте установку Nova

```bash
composer show laravel/nova
```

Если не установлена → установите (см. Решение 1).

### 2. Проверьте маршруты

```bash
php artisan route:list | grep nova
```

Если маршрутов нет → очистите кеш (см. Решение 2).

### 3. Очистите кеш

```bash
php artisan optimize:clear
```

### 4. Проверьте переменные окружения

Убедитесь, что `COMPOSER_AUTH` установлен для установки Nova.

### 5. Перезапустите приложение

После изменений Railway должен автоматически перезапустить приложение, или перезапустите вручную.

## Проверка после исправления

После выполнения решений:

1. **Проверьте маршруты:**
   ```bash
   php artisan route:list | grep nova
   ```
   Должны быть маршруты Nova.

2. **Откройте `/nova` в браузере:**
   - Должна открыться страница входа в Nova
   - Не должно быть 404

3. **Проверьте логи:**
   - Не должно быть ошибок, связанных с Nova

## Частые проблемы

### Проблема: "Package laravel/nova not found"

**Причина:** Nova не установлена через composer.

**Решение:** Установите Nova (см. Решение 1).

### Проблема: Маршруты не зарегистрированы

**Причина:** Кеш маршрутов устарел или NovaServiceProvider не загружается.

**Решение:** Очистите кеш (см. Решение 2).

### Проблема: "Class 'Laravel\Nova\...' not found"

**Причина:** Nova установлена, но классы не найдены (проблема с autoload).

**Решение:**
```bash
composer dump-autoload
php artisan config:clear
```

