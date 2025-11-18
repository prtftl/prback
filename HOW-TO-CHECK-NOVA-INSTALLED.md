# Как проверить, установлена ли Nova

## Быстрая проверка (3 способа)

### Способ 1: Проверить через composer (самый надежный)

**В Railway Shell выполните:**
```bash
composer show laravel/nova
```

**✅ Если Nova установлена:**
```
name     : laravel/nova
descrip. : A beautiful administration dashboard for Laravel
versions : * 5.7.6
```

**❌ Если Nova НЕ установлена:**
```
[InvalidArgumentException]
Package laravel/nova not found
```

---

### Способ 2: Проверить наличие папки

**В Railway Shell выполните:**
```bash
ls -la vendor/laravel/nova
```

**✅ Если Nova установлена:**
```
drwxr-xr-x  ... nova
```
(покажет содержимое папки)

**❌ Если Nova НЕ установлена:**
```
ls: cannot access 'vendor/laravel/nova': No such file or directory
```

---

### Способ 3: Проверить через PHP классы

**В Railway Shell выполните:**
```bash
php artisan tinker
```

**Затем в tinker:**
```php
class_exists(\Laravel\Nova\Nova::class)
```

**✅ Если Nova установлена:**
```
=> true
```

**❌ Если Nova НЕ установлена:**
```
=> false
```

**Выйти из tinker:**
```php
exit
```

---

## Дополнительные проверки

### Проверить маршруты Nova

**В Railway Shell:**
```bash
php artisan route:list | grep nova
```

**✅ Если Nova установлена и работает:**
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

### Проверить в логах деплоя

**В Railway:**
1. Откройте проект → Laravel сервис
2. Перейдите на вкладку **"Deployments"**
3. Откройте последний деплой
4. Просмотрите логи

**✅ Если Nova устанавливалась:**
Ищите строки:
```
Installing laravel/nova (5.7.6)
Package operations: 1 install, 0 updates, 0 removals
```

**❌ Если Nova НЕ устанавливалась:**
Этих строк не будет в логах.

---

## Пошаговая инструкция

### Шаг 1: Откройте Railway Shell

1. Откройте [railway.app](https://railway.app)
2. Войдите в свой проект
3. Откройте **Laravel сервис** (не MySQL!)
4. Перейдите на вкладку **"Shell"** или **"Terminal"**

### Шаг 2: Выполните проверку

Выберите один из способов выше (рекомендую **Способ 1**):
```bash
composer show laravel/nova
```

### Шаг 3: Интерпретируйте результат

- **Если видите информацию о пакете** → Nova установлена ✅
- **Если видите ошибку "not found"** → Nova НЕ установлена ❌

---

## Что делать, если Nova НЕ установлена?

### Причина 1: Нет переменных окружения для авторизации

**Проверьте в Railway:**
1. Откройте проект → Laravel сервис → **Variables**
2. Проверьте наличие:
   - `COMPOSER_AUTH` (полный JSON)
   - ИЛИ `COMPOSER_AUTH_NOVA_USERNAME` + `COMPOSER_AUTH_NOVA_PASSWORD`

**Если переменных нет:**
- Добавьте их в Railway Variables
- Пересоберите проект

### Причина 2: Неправильная команда build

**Проверьте в Railway:**
1. Откройте проект → Laravel сервис → **Settings**
2. Проверьте **Build Command**

**Должно быть:**
```bash
bash scripts/setup-composer-auth.sh && composer install --optimize-autoloader --no-dev
```

**Если команды нет или неправильная:**
- Исправьте Build Command
- Пересоберите проект

### Причина 3: Ошибка при установке

**Проверьте логи деплоя:**
1. Откройте последний деплой
2. Ищите ошибки при `composer install`
3. Часто бывает: "Authentication required" или "401 Unauthorized"

**Решение:**
- Настроить правильную авторизацию (см. Причину 1)

---

## Быстрая проверка всех условий

**Выполните в Railway Shell одну за другой:**

```bash
# 1. Проверить, установлена ли Nova
composer show laravel/nova

# 2. Проверить переменные окружения
echo $COMPOSER_AUTH

# 3. Проверить маршруты
php artisan route:list | grep nova

# 4. Проверить классы
php -r "echo class_exists('Laravel\Nova\Nova') ? 'YES' : 'NO';"
```

**Ожидаемый результат:**
- ✅ `composer show` → показывает информацию о Nova
- ✅ `echo $COMPOSER_AUTH` → показывает JSON (не пусто)
- ✅ `route:list` → показывает маршруты Nova
- ✅ `class_exists` → выводит "YES"

---

## Резюме

**Самый быстрый способ проверить:**
```bash
composer show laravel/nova
```

**Если команда показывает информацию о пакете** → Nova установлена ✅  
**Если команда показывает ошибку** → Nova НЕ установлена ❌

**Следующий шаг:**
Если Nova не установлена, проверьте переменные окружения и build command в Railway.

