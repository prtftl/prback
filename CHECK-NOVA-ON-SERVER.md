# Проверка Nova на сервере (Railway)

## Важно: Проверяйте на сервере, а не локально!

Проблема с 404 на `/nova` происходит на сервере Railway, поэтому нужно проверять там, а не локально.

## Пошаговая проверка в Railway Shell

### Шаг 1: Откройте Railway Shell

1. Откройте Railway → ваш проект
2. Откройте **Laravel сервис** (не MySQL)
3. Перейдите в раздел **Shell** или **Terminal**

### Шаг 2: Проверьте, установлена ли Nova

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
- Nova не установлена на сервере
- Нужно установить через composer

### Шаг 3: Проверьте маршруты Nova

В Railway Shell выполните:

```bash
php artisan route:list | grep nova
```

**Ожидаемый результат:**
Должны быть маршруты, начинающиеся с `/nova`:
```
GET|HEAD  nova .................... nova.login
POST      nova/login .............. nova.login
GET|HEAD  nova-api/...
```

**Если маршрутов нет:**
- Маршруты Nova не зарегистрированы
- Проблема с `NovaServiceProvider` или Nova не установлена

### Шаг 4: Проверьте переменные окружения

В Railway Shell выполните:

```bash
echo $COMPOSER_AUTH
```

**Ожидаемый результат:**
Должна быть строка с JSON (начинается с `{"http-basic":...}`)

**Если пусто:**
- `COMPOSER_AUTH` не установлен
- Нужно добавить в Railway Variables

### Шаг 5: Проверьте логи на сервере

В Railway Shell выполните:

```bash
tail -n 50 storage/logs/laravel.log
```

Ищите ошибки, связанные с Nova.

## Если Nova не установлена на сервере

### Решение: Установить Nova через Railway Shell

В Railway Shell выполните:

```bash
# 1. Проверьте, что COMPOSER_AUTH установлен
echo $COMPOSER_AUTH

# 2. Если нет, проверьте переменные окружения
env | grep COMPOSER_AUTH

# 3. Если COMPOSER_AUTH не установлен, но есть отдельные переменные:
#    Создайте auth.json
php scripts/setup-auth.php

# 4. Установите Nova
composer require laravel/nova:5.7.6 --no-interaction

# 5. Опубликуйте ресурсы Nova
php artisan nova:install

# 6. Очистите кеш
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan optimize:clear
```

## Если Nova установлена, но маршрутов нет

### Решение: Очистить кеш и перезагрузить

В Railway Shell выполните:

```bash
# Очистите все кеши
php artisan optimize:clear

# Проверьте маршруты снова
php artisan route:list | grep nova

# Если маршрутов все еще нет, проверьте провайдеры
php artisan tinker
# В tinker:
class_exists(\Laravel\Nova\Nova::class);
# Должно вернуть true
```

## Проверка переменных окружения в Railway

В Railway → Variables проверьте:

**Для установки Nova:**
- `COMPOSER_AUTH` - должен быть установлен (JSON строка)
- Или `COMPOSER_AUTH_NOVA_USERNAME` + `COMPOSER_AUTH_NOVA_PASSWORD`

**Для работы Nova:**
- `NOVA_LICENSE_KEY` (опционально, но рекомендуется)

## После установки Nova

1. **Проверьте маршруты:**
   ```bash
   php artisan route:list | grep nova
   ```

2. **Откройте `/nova` в браузере:**
   - Должна открыться страница входа в Nova
   - Не должно быть 404

3. **Проверьте логи:**
   ```bash
   tail -n 50 storage/logs/laravel.log
   ```

## Важно

- ✅ Проверяйте на **сервере** (Railway Shell), а не локально
- ✅ Убедитесь, что `COMPOSER_AUTH` установлен в Railway Variables
- ✅ После установки Nova выполните `php artisan nova:install`
- ✅ Очистите кеш после установки

## Типичная проблема

**Проблема:** Nova установлена локально, но не установлена на сервере.

**Причина:** `composer install` на сервере не установил Nova, потому что:
- `COMPOSER_AUTH` не установлен
- Ошибка при установке зависимостей

**Решение:** Установите Nova через Railway Shell (см. выше).

