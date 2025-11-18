# Быстрая проверка 404 Nova на продакшене

## Проблема
`https://prback-production.up.railway.app/nova` → 404

## Быстрая диагностика (3 команды в Railway Shell)

```bash
# 1. Проверить, установлена ли Nova
composer show laravel/nova

# 2. Проверить маршруты
php artisan route:list | grep nova | head -3

# 3. Проверить классы
php -r "echo class_exists('Laravel\Nova\Nova') ? 'Nova: YES' : 'Nova: NO';"
```

---

## Результаты и решения

### ❌ Если Nova НЕ установлена (`Package laravel/nova not found`)

**Проблема:** Nova не установилась при деплое

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

3. **Пересобрать проект:**
   - Сохраните настройки
   - Railway автоматически запустит новый деплой
   - Проверьте логи деплоя на наличие `Installing laravel/nova (5.7.6)`

---

### ❌ Если маршрутов нет (пустой вывод)

**Проблема:** Nova установлена, но маршруты не зарегистрированы

**Решение:**
```bash
# В Railway Shell
php artisan config:clear
php artisan route:clear
php artisan cache:clear

# Если Nova установлена, но не настроена:
php artisan nova:install
```

---

### ❌ Если классы не существуют (`Nova: NO`)

**Проблема:** Nova не установлена или не загружается

**Решение:**
```bash
# В Railway Shell
composer require laravel/nova:5.7.6 --no-interaction
php artisan nova:install
php artisan config:clear
php artisan route:clear
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

---

## Если ничего не помогло - установить вручную

**В Railway Shell выполните:**
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

---

## Наиболее вероятная причина

**90% вероятность:** Nova не установилась из-за отсутствия переменных окружения `COMPOSER_AUTH_NOVA_USERNAME` и `COMPOSER_AUTH_NOVA_PASSWORD` в Railway Variables.

**Что сделать:**
1. Добавить переменные в Railway Variables
2. Пересобрать проект
3. Проверить логи деплоя

