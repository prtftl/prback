# Анализ логов деплоя Nova

## Что видно в логах

### Выполняемые команды:
```
composer install --optimize-autoloader --no-scripts --no-interaction --ignore-platform-req=ext-zip && php artisan nova:install
```

### Что происходит:
1. ✅ Выполняется `composer install` с флагом `--no-scripts`
2. ✅ Устанавливается `composer/semver (3.4.4)` - Composer работает
3. ⚠️ **НЕ видно установки `laravel/nova`** в логах
4. ⚠️ **НЕ видно результата выполнения `php artisan nova:install`**

---

## Проблемы, которые видно из логов

### ❌ Проблема 1: Nova не устанавливается

**Ожидалось в логах:**
```
Installing laravel/nova (5.7.6)
Package operations: 1 install, 0 updates, 0 removals
```

**В логах этого НЕТ** → Nova не установилась через `composer install`

### ❌ Проблема 2: Результат `nova:install` не виден

Команда `php artisan nova:install` выполняется, но:
- Не видно успешного сообщения
- Не видно ошибок
- Не видно публикации ассетов Nova

---

## Возможные причины

### 1. Отсутствует COMPOSER_AUTH (наиболее вероятно)

**Проблема:** Railway не может скачать Nova, потому что нет авторизации для приватного репозитория.

**Проверка в Railway:**
1. Откройте проект → Laravel сервис → **Variables**
2. Проверьте наличие:
   - `COMPOSER_AUTH` (полный JSON)
   - ИЛИ `COMPOSER_AUTH_NOVA_USERNAME` + `COMPOSER_AUTH_NOVA_PASSWORD`

**Если переменных нет:**
- Nova не скачается, даже если команда правильная
- `composer install` пропустит Nova без ошибки (если пакет не найден)

### 2. Команда `nova:install` выполняется до установки Nova

**Проблема:** Если Nova не установлена через `composer install`, то команда `php artisan nova:install` не сработает, потому что классы Nova не существуют.

**Что происходит:**
```bash
composer install  # Nova не установилась (нет auth)
php artisan nova:install  # Ошибка: классы Nova не найдены
```

### 3. Флаг `--no-scripts` отключает post-install-cmd

**Важно:** Флаг `--no-scripts` отключает выполнение скриптов из `composer.json`, включая:
```json
"post-install-cmd": [
    "@php artisan nova:install || true"
]
```

**Это нормально**, потому что команда `php artisan nova:install` выполняется отдельно после `composer install`.

---

## Что нужно проверить

### ✅ Шаг 1: Проверить переменные окружения в Railway

**В Railway:**
1. Проект → Laravel сервис → **Variables**
2. Проверьте наличие одной из комбинаций:

**Вариант A:**
```
COMPOSER_AUTH={"http-basic":{"nova.laravel.com":{"username":"YOUR_EMAIL","password":"YOUR_LICENSE_KEY"}}}
```

**Вариант B:**
```
COMPOSER_AUTH_NOVA_USERNAME=YOUR_EMAIL
COMPOSER_AUTH_NOVA_PASSWORD=YOUR_LICENSE_KEY
```

**Если переменных нет → добавить их!**

### ✅ Шаг 2: Проверить Build Command в Railway

**В Railway:**
1. Проект → Laravel сервис → **Settings**
2. Проверьте **Build Command**

**Должно быть:**
```bash
bash scripts/setup-composer-auth.sh && composer install --optimize-autoloader --no-scripts --no-interaction --ignore-platform-req=ext-zip && php artisan nova:install
```

**Или если используете COMPOSER_AUTH напрямую:**
```bash
composer install --optimize-autoloader --no-scripts --no-interaction --ignore-platform-req=ext-zip && php artisan nova:install
```

### ✅ Шаг 3: Проверить, установилась ли Nova (после деплоя)

**В Railway Shell:**
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

### ✅ Шаг 4: Проверить полные логи деплоя

**В Railway:**
1. Откройте последний деплой
2. Просмотрите **ВСЕ логи** (не только начало)
3. Ищите:
   - `Installing laravel/nova` - Nova устанавливается ✅
   - `401 Unauthorized` - проблема с авторизацией ❌
   - `Authentication required` - нет COMPOSER_AUTH ❌
   - `Publishing Nova Assets` - Nova установилась и публикуется ✅

---

## Что должно быть в логах при успешной установке

### Правильная последовательность:

```
1. composer install --optimize-autoloader --no-scripts --no-interaction --ignore-platform-req=ext-zip
   → Installing dependencies from lock file
   → Installing laravel/nova (5.7.6)  ← ЭТО ДОЛЖНО БЫТЬ!
   → Package operations: 1 install, 0 updates, 0 removals

2. php artisan nova:install
   → Publishing Nova Assets / Resources  ← ЭТО ДОЛЖНО БЫТЬ!
   → Nova scaffolding installed successfully  ← ЭТО ДОЛЖНО БЫТЬ!
```

### Что видно в ваших логах:

```
1. composer install
   → Installing composer/semver (3.4.4)  ← Только это видно
   → НЕТ "Installing laravel/nova"  ← ПРОБЛЕМА!

2. php artisan nova:install
   → Нет вывода команды  ← ПРОБЛЕМА!
```

---

## Рекомендации

### 1. Добавить переменные окружения

**Если их нет, добавьте в Railway Variables:**
```
COMPOSER_AUTH_NOVA_USERNAME=ваш_email@example.com
COMPOSER_AUTH_NOVA_PASSWORD=ваш_license_key
```

### 2. Обновить Build Command

**Убедитесь, что Build Command включает настройку auth:**
```bash
bash scripts/setup-composer-auth.sh && composer install --optimize-autoloader --no-scripts --no-interaction --ignore-platform-req=ext-zip && php artisan nova:install
```

### 3. Пересобрать проект

После добавления переменных:
1. Сохраните настройки
2. Railway автоматически запустит новый деплой
3. Проверьте логи нового деплоя
4. Ищите строку `Installing laravel/nova`

### 4. Проверить результат

После деплоя в Railway Shell:
```bash
composer show laravel/nova
php artisan route:list | grep nova
```

---

## Быстрая диагностика

**Выполните в Railway Shell после деплоя:**

```bash
# 1. Проверить, установлена ли Nova
composer show laravel/nova

# 2. Проверить переменные окружения
echo $COMPOSER_AUTH
echo $COMPOSER_AUTH_NOVA_USERNAME

# 3. Проверить маршруты
php artisan route:list | grep nova

# 4. Проверить классы
php -r "echo class_exists('Laravel\Nova\Nova') ? 'YES' : 'NO';"
```

**Ожидаемый результат:**
- ✅ `composer show` → показывает информацию о Nova
- ✅ `echo $COMPOSER_AUTH_NOVA_USERNAME` → показывает ваш email (не пусто)
- ✅ `route:list` → показывает маршруты Nova
- ✅ `class_exists` → выводит "YES"

---

## Резюме

**Из ваших логов видно:**
1. ❌ Nova НЕ устанавливается через `composer install`
2. ❌ Команда `php artisan nova:install` не дает видимого результата
3. ⚠️ Скорее всего, проблема в отсутствии `COMPOSER_AUTH` или неправильной настройке

**Что делать:**
1. Проверить переменные окружения в Railway
2. Добавить `COMPOSER_AUTH` или `COMPOSER_AUTH_NOVA_USERNAME` + `COMPOSER_AUTH_NOVA_PASSWORD`
3. Обновить Build Command, чтобы включить настройку auth
4. Пересобрать проект
5. Проверить логи нового деплоя на наличие `Installing laravel/nova`

