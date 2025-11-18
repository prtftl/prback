# Проверка и установка Nova без Shell

## Альтернативные способы

### Способ 1: Через логи деплоя

#### Шаг 1: Проверьте логи последнего деплоя

1. В Railway откройте ваш проект
2. Откройте **Laravel сервис**
3. Перейдите на вкладку **"Deployments"**
4. Откройте последний деплой
5. Нажмите **"View Logs"** или просто просмотрите логи

#### Шаг 2: Ищите в логах

Ищите в логах деплоя:

**Если Nova установлена:**
- Должна быть строка: `Installing laravel/nova (5.7.6)`
- Или: `Package operations: X installs` (где X > 0)

**Если Nova НЕ установлена:**
- Не будет упоминаний о `laravel/nova`
- Может быть ошибка: `Package laravel/nova not found`

### Способ 2: Через переменные окружения

#### Проверьте переменные

1. В Railway откройте **Laravel сервис**
2. Перейдите на вкладку **"Variables"**
3. Проверьте наличие:

**Для установки Nova:**
- `COMPOSER_AUTH` - должен быть установлен
- Или `COMPOSER_AUTH_NOVA_USERNAME` + `COMPOSER_AUTH_NOVA_PASSWORD`

**Если переменных нет:**
- Добавьте их (см. инструкции ниже)
- Railway автоматически перезапустит деплой
- Nova должна установиться автоматически

### Способ 3: Через настройки деплоя (Deploy Commands)

#### Добавьте команды в настройки деплоя

1. В Railway откройте **Laravel сервис**
2. Перейдите на вкладку **"Settings"** или **"Deploy"**
3. Найдите раздел **"Build Command"** или **"Deploy Command"**
4. Добавьте команды для установки Nova:

```bash
php scripts/setup-auth.php && composer require laravel/nova:5.7.6 --no-interaction && php artisan nova:install
```

Или если используете `COMPOSER_AUTH` напрямую:

```bash
composer require laravel/nova:5.7.6 --no-interaction && php artisan nova:install
```

5. Сохраните настройки
6. Railway автоматически запустит новый деплой

### Способ 4: Через веб-интерфейс (проверка работы)

#### Проверьте через браузер

1. Откройте ваш сайт: `https://prback-production.up.railway.app/nova`
2. **Если 404:**
   - Nova не установлена или не зарегистрирована
   - Нужно установить (см. способы выше)

3. **Если страница входа в Nova:**
   - Nova установлена и работает! ✅
   - Можете войти (если есть пользователь)

4. **Если ошибка 500:**
   - Nova установлена, но есть проблема с конфигурацией
   - Проверьте логи деплоя

## Установка Nova без Shell

### Вариант 1: Через переменные окружения (автоматически)

#### Шаг 1: Добавьте переменные

В Railway → Laravel сервис → Variables добавьте:

**Если используете Вариант 1 (рекомендуется):**
```
COMPOSER_AUTH={"http-basic":{"nova.laravel.com":{"username":"maksstepenko@gmail.com","password":"GTsoRsPwzmB7xEYfipIMDo4o0E3FpGhXwlzfC47fam9733HSAl"}}}
```

**Если используете Вариант 2:**
```
COMPOSER_AUTH_NOVA_USERNAME=maksstepenko@gmail.com
COMPOSER_AUTH_NOVA_PASSWORD=GTsoRsPwzmB7xEYfipIMDo4o0E3FpGhXwlzfC47fam9733HSAl
```

#### Шаг 2: Обновите composer.json

Убедитесь, что в `composer.json` есть:

```json
"repositories": [
    {
        "type": "composer",
        "url": "https://nova.laravel.com"
    }
],
"require": {
    "laravel/nova": "5.7.6"
}
```

#### Шаг 3: Запустите новый деплой

1. Сделайте коммит и пуш изменений (если нужно)
2. Railway автоматически запустит новый деплой
3. При деплое выполнится `composer install`, который установит Nova

#### Шаг 4: Добавьте команду для публикации Nova

В Railway → Settings → Build Command добавьте после `composer install`:

```bash
composer install --optimize-autoloader --no-scripts --no-interaction --ignore-platform-req=ext-zip && php artisan nova:install
```

### Вариант 2: Через настройки деплоя

#### Добавьте команды в Build Command

В Railway → Laravel сервис → Settings → Build Command:

```bash
php scripts/setup-auth.php && composer install --optimize-autoloader --no-scripts --no-interaction --ignore-platform-req=ext-zip && php artisan nova:install
```

Или если используете `COMPOSER_AUTH`:

```bash
composer install --optimize-autoloader --no-scripts --no-interaction --ignore-platform-req=ext-zip && php artisan nova:install
```

## Проверка без Shell

### Способ 1: Через логи деплоя

1. Откройте последний деплой
2. Просмотрите логи
3. Ищите:
   - `Installing laravel/nova` - Nova устанавливается
   - `Publishing Nova Assets` - Nova публикуется
   - Ошибки с `laravel/nova` - проблема с установкой

### Способ 2: Через веб-интерфейс

1. Откройте `/nova` в браузере
2. **404** = Nova не установлена или не зарегистрирована
3. **Страница входа** = Nova работает! ✅
4. **500** = Проблема с конфигурацией

### Способ 3: Через переменные окружения

Проверьте в Railway Variables:
- Есть ли `COMPOSER_AUTH`?
- Есть ли `NOVA_LICENSE_KEY`?

## Пошаговая инструкция (без Shell)

### 1. Проверьте переменные окружения

Railway → Laravel сервис → Variables:
- ✅ `COMPOSER_AUTH` установлен?
- ✅ `DB_CONNECTION=mysql` установлен?
- ✅ `DB_URL` установлен?

### 2. Проверьте composer.json

Убедитесь, что в репозитории есть:
- Репозиторий Nova
- Пакет `laravel/nova:5.7.6`

### 3. Запустите новый деплой

1. Сделайте коммит и пуш (если нужно)
2. Или в Railway нажмите "Redeploy"
3. Railway автоматически выполнит `composer install`

### 4. Проверьте логи деплоя

После деплоя:
1. Откройте последний деплой
2. Просмотрите логи
3. Ищите установку Nova

### 5. Проверьте через браузер

Откройте `/nova` - должна открыться страница входа.

## Если Nova все еще не работает

### Проверьте логи деплоя на ошибки:

1. **Ошибка аутентификации:**
   - Проверьте `COMPOSER_AUTH`
   - Убедитесь, что email и license key правильные

2. **Ошибка установки:**
   - Проверьте логи деплоя
   - Убедитесь, что репозиторий Nova добавлен в `composer.json`

3. **404 после установки:**
   - Проверьте, что `php artisan nova:install` выполнился
   - Проверьте логи на ошибки

## Резюме

**Без Shell можно:**
- ✅ Проверить логи деплоя
- ✅ Проверить переменные окружения
- ✅ Добавить команды в настройки деплоя
- ✅ Проверить через веб-интерфейс
- ✅ Установить Nova через автоматический деплой

**Главное:**
1. Убедитесь, что `COMPOSER_AUTH` установлен
2. Убедитесь, что Nova в `composer.json`
3. Запустите новый деплой
4. Проверьте логи и веб-интерфейс

