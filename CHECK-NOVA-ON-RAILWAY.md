# Проверка установки Nova на Railway

## Как проверить, установлена ли Nova на Railway

### Шаг 1: Откройте Railway Shell

1. Откройте [railway.app](https://railway.app)
2. Войдите в свой проект
3. Откройте **Laravel сервис** (не MySQL!)
4. Перейдите на вкладку **"Shell"** или **"Terminal"**

### Шаг 2: Выполните команды проверки

**В Railway Shell выполните по очереди:**

```bash
# 1. Проверить, установлена ли Nova
composer show laravel/nova
```

**✅ Если установлена:**
```
name     : laravel/nova
descrip. : A wonderful administration interface for Laravel.
versions : * 5.7.6
```

**❌ Если НЕ установлена:**
```
[InvalidArgumentException]
Package laravel/nova not found
```

---

```bash
# 2. Проверить маршруты Nova
php artisan route:list | grep nova | head -5
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

---

```bash
# 3. Проверить классы Nova
php -r "echo class_exists('Laravel\Nova\Nova') ? 'Nova: YES' : 'Nova: NO';"
```

**✅ Если классы есть:**
```
Nova: YES
```

**❌ Если классов нет:**
```
Nova: NO
```

---

```bash
# 4. Проверить папку vendor
ls -la vendor/laravel/nova | head -5
```

**✅ Если папка есть:**
```
drwxr-xr-x  ... nova
(покажет содержимое)
```

**❌ Если папки нет:**
```
ls: cannot access 'vendor/laravel/nova': No such file or directory
```

---

## Что делать в зависимости от результата

### ❌ Если Nova НЕ установлена

**Проблема:** `composer show laravel/nova` → `Package laravel/nova not found`

**Решение:**

1. **Проверить переменные окружения в Railway:**
   - Railway → Laravel сервис → **Variables**
   - Должны быть:
     - `COMPOSER_AUTH_NOVA_USERNAME` = ваш email
     - `COMPOSER_AUTH_NOVA_PASSWORD` = ваш license key
     - ИЛИ `COMPOSER_AUTH` = полный JSON

2. **Проверить Build Command:**
   - Railway → Laravel сервис → **Settings** → **Build Command**
   - Должно быть:
     ```bash
     bash scripts/setup-composer-auth.sh && composer run deploy
     ```

3. **Установить Nova вручную (в Railway Shell):**
   ```bash
   # Настроить auth
   bash scripts/setup-composer-auth.sh
   
   # Установить Nova
   composer require laravel/nova:5.7.6 --no-interaction
   
   # Установить ассеты
   php artisan nova:install
   
   # Очистить кеш
   php artisan config:clear
   php artisan route:clear
   php artisan cache:clear
   ```

---

### ❌ Если маршрутов нет

**Проблема:** Nova установлена, но маршруты не зарегистрированы

**Решение:**

```bash
# В Railway Shell
php artisan nova:install
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

**Затем проверьте снова:**
```bash
php artisan route:list | grep nova
```

---

### ❌ Если классы не существуют

**Проблема:** `Nova: NO`

**Решение:**

```bash
# В Railway Shell
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

**Если не помогло:**
```bash
composer require laravel/nova:5.7.6 --no-interaction
php artisan nova:install
```

---

## Проверка логов последнего деплоя

**В Railway:**
1. Проект → Laravel сервис → **Deployments**
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

## Быстрая проверка (одна команда)

**В Railway Shell выполните:**
```bash
composer show laravel/nova && echo "---" && php artisan route:list | grep nova | head -3 && echo "---" && php -r "echo class_exists('Laravel\Nova\Nova') ? 'Nova classes: YES' : 'Nova classes: NO';"
```

**Ожидаемый результат:**
```
name     : laravel/nova
versions : * 5.7.6
---
GET|HEAD  nova .................... nova.login
POST      nova/login .............. nova.login
---
Nova classes: YES
```

---

## Резюме

**Проверка на Railway:**
1. Откройте Railway Shell
2. Выполните `composer show laravel/nova`
3. Выполните `php artisan route:list | grep nova`
4. Выполните `php -r "echo class_exists('Laravel\Nova\Nova') ? 'YES' : 'NO';"`

**Если Nova не установлена:**
- Проверьте переменные окружения
- Установите вручную через Railway Shell
- Или пересоберите проект с правильными настройками

