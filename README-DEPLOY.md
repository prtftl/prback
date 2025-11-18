# Deploy Configuration

## Required Environment Variables

### For Nova Authentication (Composer) - ОБЯЗАТЕЛЬНО

**ВАЖНО:** Для установки Nova через Composer нужны ТОЛЬКО переменные для аутентификации (1 или 2 переменные).

**Вариант 1: Использование COMPOSER_AUTH (рекомендуется)**

Установите переменную окружения `COMPOSER_AUTH` со следующим значением (JSON в одну строку, без пробелов):

```bash
COMPOSER_AUTH={"http-basic":{"nova.laravel.com":{"username":"prtftl","password":"GTsoRsPwzmB7xEYfipIMDo4o0E3FpGhXwlzfC47fam9733HSAl"}}}
```

**Важно:** Убедитесь, что значение не содержит пробелов и правильно экранировано для вашей платформы деплоя.

**Вариант 2: Использование отдельных переменных**

Если ваша платформа деплоя поддерживает создание файлов перед установкой, используйте:

```bash
COMPOSER_AUTH_NOVA_USERNAME=prtftl
COMPOSER_AUTH_NOVA_PASSWORD=GTsoRsPwzmB7xEYfipIMDo4o0E3FpGhXwlzfC47fam9733HSAl
```

И выполните перед `composer install`:
```bash
./scripts/setup-composer-auth.sh
```

**Примечание:** Скрипт автоматически создаст `auth.json` или установит `COMPOSER_AUTH` из отдельных переменных.

### For Nova Admin User (ОПЦИОНАЛЬНО)

Эти переменные нужны только для автоматического создания администратора Nova. Можно добавить позже или создать пользователя вручную через Nova интерфейс.

```bash
NOVA_USER_EMAIL=admin@example.com
NOVA_USER_PASSWORD=your-secure-password
NOVA_USER_NAME=Administrator
NOVA_LICENSE_KEY=GTsoRsPwzmB7xEYfipIMDo4o0E3FpGhXwlzfC47fam9733HSAl
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

