# Проверка 404 Nova на продакшене

## Проблема
После деплоя Nova все еще возвращает 404: `https://prback-production.up.railway.app/nova`

## Что нужно проверить

### 1. ✅ Проверить, установилась ли Nova на Railway

**В Railway Shell выполните:**
```bash
composer show laravel/nova
```

**✅ Если установлена:**
```
name     : laravel/nova
versions : * 5.7.6
```

**❌ Если НЕ установлена:**
```
[InvalidArgumentException]
Package laravel/nova not found
```

**Если не установлена → переходим к шагу 2**

---

### 2. ✅ Проверить переменные окружения

**В Railway Shell:**
```bash
echo "Username: $COMPOSER_AUTH_NOVA_USERNAME"
echo "Password set: $([ -n "$COMPOSER_AUTH_NOVA_PASSWORD" ] && echo 'YES' || echo 'NO')"
echo "COMPOSER_AUTH: ${COMPOSER_AUTH:0:50}..."
```

**Должно быть:**
- `COMPOSER_AUTH_NOVA_USERNAME` → ваш email (не пусто)
- `COMPOSER_AUTH_NOVA_PASSWORD` → YES
- ИЛИ `COMPOSER_AUTH` → JSON строка (не пусто)

**Если пусто → нужно добавить переменные в Railway Variables**

---

### 3. ✅ Проверить логи последнего деплоя

**В Railway:**
1. Откройте проект → Laravel сервис → **Deployments**
2. Откройте последний деплой
3. Просмотрите логи

**Ищите:**
- ✅ `Installing laravel/nova (5.7.6)` - Nova устанавливается
- ✅ `Publishing Nova Assets` - Nova публикуется
- ✅ `Nova scaffolding installed successfully` - Nova установлена
- ❌ `401 Unauthorized` - проблема с авторизацией
- ❌ `Authentication required` - нет COMPOSER_AUTH
- ❌ `Package laravel/nova not found` - Nova не установилась

---

### 4. ✅ Проверить маршруты Nova

**В Railway Shell:**
```bash
php artisan route:list | grep nova
```

**✅ Если маршруты есть:**
```
GET|HEAD  nova .................... nova.login
POST      nova/login .............. nova.login
GET|HEAD  nova-api/...
```

**❌ Если маршрутов нет:**
```
(пустой вывод)
```

**Если маршрутов нет → проблема с регистрацией провайдера**

---

### 5. ✅ Проверить регистрацию NovaServiceProvider

**В Railway Shell:**
```bash
php artisan tinker
```

**Затем в tinker:**
```php
class_exists('Laravel\Nova\Nova')
// Должно вернуть: true

app()->getLoadedProviders()
// Ищите: App\Providers\NovaServiceProvider
```

**Если класс не существует или провайдер не загружен:**
- Nova не установлена
- Или провайдер не регистрируется

---

### 6. ✅ Проверить Build Command в Railway

**В Railway:**
1. Проект → Laravel сервис → **Settings**
2. Проверьте **Build Command**

**Должно быть что-то вроде:**
```bash
bash scripts/setup-composer-auth.sh && composer run deploy
```

**Или:**
```bash
bash scripts/setup-composer-auth.sh && composer install --optimize-autoloader --no-interaction --ignore-platform-req=ext-zip
```

**Проверьте:**
- ✅ Есть ли настройка auth перед `composer install`?
- ✅ Используется ли правильная команда?
- ❌ Нет ли флага `--no-scripts` (его не должно быть!)

---

## Быстрая диагностика (выполните в Railway Shell)

```bash
# 1. Проверить Nova
composer show laravel/nova

# 2. Проверить переменные
env | grep COMPOSER

# 3. Проверить auth.json
ls -la auth.json && cat auth.json

# 4. Проверить маршруты
php artisan route:list | grep nova | head -5

# 5. Проверить классы
php -r "echo class_exists('Laravel\Nova\Nova') ? 'YES' : 'NO';"

# 6. Проверить провайдеры
php artisan tinker --execute="var_dump(array_key_exists('App\Providers\NovaServiceProvider', app()->getLoadedProviders()));"
```

---

## Решения в зависимости от проблемы

### Проблема 1: Nova не установлена

**Решение:**
1. Проверьте переменные окружения в Railway Variables
2. Добавьте `COMPOSER_AUTH_NOVA_USERNAME` и `COMPOSER_AUTH_NOVA_PASSWORD`
3. Или добавьте `COMPOSER_AUTH` (полный JSON)
4. Пересоберите проект

### Проблема 2: Маршруты не зарегистрированы

**Решение:**
1. Проверьте, что Nova установлена (`composer show laravel/nova`)
2. Проверьте, что классы существуют
3. Выполните в Railway Shell:
   ```bash
   php artisan config:clear
   php artisan route:clear
   php artisan cache:clear
   ```
4. Перезапустите приложение

### Проблема 3: Провайдер не загружается

**Проверьте `bootstrap/providers.php`:**
```php
if (class_exists(\Laravel\Nova\Nova::class) && 
    class_exists(\Laravel\Nova\NovaApplicationServiceProvider::class)) {
    $providers[] = App\Providers\NovaServiceProvider::class;
}
```

**Если классы не существуют:**
- Nova не установлена
- Нужно установить через `composer install`

### Проблема 4: Скрипт post-install-cmd не выполнился

**Проверьте логи деплоя:**
- Должна быть строка `Publishing Nova Assets`
- Должна быть строка `Nova scaffolding installed successfully`

**Если нет:**
- Возможно, скрипт не выполнился
- Выполните вручную в Railway Shell:
  ```bash
  php artisan nova:install
  ```

---

## Пошаговое решение

### Шаг 1: Проверить переменные окружения

**В Railway:**
1. Проект → Laravel сервис → **Variables**
2. Убедитесь, что есть:
   - `COMPOSER_AUTH_NOVA_USERNAME` = ваш email
   - `COMPOSER_AUTH_NOVA_PASSWORD` = ваш license key
   - ИЛИ `COMPOSER_AUTH` = полный JSON

### Шаг 2: Проверить Build Command

**В Railway:**
1. Проект → Laravel сервис → **Settings**
2. **Build Command** должен быть:
   ```bash
   bash scripts/setup-composer-auth.sh && composer run deploy
   ```

### Шаг 3: Пересобрать проект

1. Сохраните настройки
2. Railway автоматически запустит новый деплой
3. Дождитесь завершения

### Шаг 4: Проверить логи деплоя

**В логах должно быть:**
```
Installing laravel/nova (5.7.6)
Publishing Nova Assets / Resources
Nova scaffolding installed successfully
```

### Шаг 5: Проверить в Railway Shell

```bash
composer show laravel/nova
php artisan route:list | grep nova
```

---

## Если ничего не помогло

### Вариант 1: Установить Nova вручную в Railway Shell

```bash
# 1. Настроить auth
bash scripts/setup-composer-auth.sh

# 2. Установить Nova
composer require laravel/nova:5.7.6 --no-interaction

# 3. Установить ассеты
php artisan nova:install

# 4. Очистить кеш
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

### Вариант 2: Проверить конфигурацию Nova

**В Railway Shell:**
```bash
php artisan tinker
```

```php
config('nova.path')
// Должно быть: '/nova'

config('nova.license_key')
// Должен быть ваш license key (или null)
```

---

## Резюме

**Наиболее вероятные причины 404:**
1. Nova не установлена (80%) - нет переменных окружения
2. Маршруты не зарегистрированы (15%) - провайдер не загружается
3. Проблема с кешем (5%) - нужно очистить кеш

**Что проверить в первую очередь:**
1. ✅ `composer show laravel/nova` - установлена ли?
2. ✅ `php artisan route:list | grep nova` - есть ли маршруты?
3. ✅ Логи деплоя - установилась ли Nova?

