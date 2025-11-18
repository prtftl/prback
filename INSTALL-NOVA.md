# Установка Nova после деплоя

Этот проект настроен для работы без Nova. После успешного деплоя вы можете установить Nova.

## Шаг 1: Добавьте переменные окружения в Railway

Добавьте переменные для аутентификации Nova:

**Вариант 1 (рекомендуется):**
```
COMPOSER_AUTH={"http-basic":{"nova.laravel.com":{"username":"prtftl","password":"GTsoRsPwzmB7xEYfipIMDo4o0E3FpGhXwlzfC47fam9733HSAl"}}}
```

**Вариант 2:**
```
COMPOSER_AUTH_NOVA_USERNAME=prtftl
COMPOSER_AUTH_NOVA_PASSWORD=GTsoRsPwzmB7xEYfipIMDo4o0E3FpGhXwlzfC47fam9733HSAl
```

## Шаг 2: Обновите composer.json

Добавьте Nova обратно в `composer.json`:

```json
"require": {
    "php": "^8.2",
    "laravel/framework": "^12.0",
    "laravel/nova": "5.7.6",
    "laravel/sanctum": "^4.2",
    "laravel/tinker": "^2.10.1"
},
"require-dev": {
    "fakerphp/faker": "^1.23",
    "laravel/nova-devtool": "^1.8",
    "laravel/pail": "^1.2.2",
    "laravel/pint": "^1.24",
    "laravel/sail": "^1.41",
    "mockery/mockery": "^1.6",
    "nunomaduro/collision": "^8.6",
    "phpunit/phpunit": "^11.5.3"
},
```

И убедитесь, что репозиторий Nova добавлен:

```json
"repositories": [
    {
        "type": "composer",
        "url": "https://nova.laravel.com"
    }
],
```

## Шаг 3: Установите Nova

### Через Railway Shell:

1. Откройте Railway Shell для вашего проекта
2. Выполните:

```bash
# Если используете Вариант 2, создайте auth.json
php scripts/setup-auth.php

# Установите Nova
composer require laravel/nova:5.7.6 --no-interaction

# Установите Nova DevTool (опционально)
composer require laravel/nova-devtool --dev --no-interaction

# Опубликуйте ресурсы Nova
php artisan nova:install
```

### Или через новый деплой:

После обновления `composer.json` и добавления переменных окружения, Railway автоматически выполнит `composer install` при следующем деплое.

## Шаг 4: Создайте пользователя Nova

Добавьте переменные для создания администратора:

```
NOVA_USER_EMAIL=admin@example.com
NOVA_USER_PASSWORD=your-secure-password
NOVA_USER_NAME=Administrator
NOVA_LICENSE_KEY=GTsoRsPwzmB7xEYfipIMDo4o0E3FpGhXwlzfC47fam9733HSAl
```

И выполните:

```bash
php artisan db:seed --force
```

## Примечание

Проект настроен так, что NovaServiceProvider загружается только если пакет Nova установлен. Это позволяет деплоить проект без Nova и установить его позже.

