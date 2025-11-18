# Deploy Configuration

## Required Environment Variables

### For Nova Authentication (Composer)

Для установки Laravel Nova через Composer требуется аутентификация. Добавьте следующие переменные окружения:

**Вариант 1: Использование COMPOSER_AUTH (рекомендуется)**

Установите переменную окружения `COMPOSER_AUTH` со следующим значением (JSON в одну строку, без пробелов):

```bash
COMPOSER_AUTH={"http-basic":{"nova.laravel.com":{"username":"maksstepenko@gmail.com","password":"#a0420121-c03c-4856-a791-c408d579fdb6"}}}
```

**Важно:** Убедитесь, что значение не содержит пробелов и правильно экранировано для вашей платформы деплоя.

**Вариант 2: Использование отдельных переменных**

Если ваша платформа деплоя поддерживает создание файлов перед установкой, используйте:

```bash
COMPOSER_AUTH_NOVA_USERNAME=maksstepenko@gmail.com
COMPOSER_AUTH_NOVA_PASSWORD=#a0420121-c03c-4856-a791-c408d579fdb6
```

И выполните перед `composer install`:
```bash
./scripts/setup-composer-auth.sh
```

**Примечание:** Скрипт автоматически создаст `auth.json` или установит `COMPOSER_AUTH` из отдельных переменных.

### For Nova Admin User

```bash
NOVA_USER_EMAIL=admin@example.com
NOVA_USER_PASSWORD=your-secure-password
NOVA_USER_NAME=Administrator
NOVA_LICENSE_KEY=#a0420121-c03c-4856-a791-c408d579fdb6
```

## Deploy Command

**ВАЖНО:** Перед выполнением `composer install` необходимо создать `auth.json`:

```bash
# Создать auth.json из переменных окружения
php scripts/setup-auth.php

# Затем выполнить установку
composer install --optimize-autoloader --no-scripts --no-interaction --ignore-platform-req=ext-zip
```

Или если используете COMPOSER_AUTH напрямую:

```bash
composer install --optimize-autoloader --no-scripts --no-interaction --ignore-platform-req=ext-zip
```

## После деплоя

Выполните миграции и сидер для создания пользователя Nova:

```bash
php artisan migrate --force
php artisan db:seed --force
```

