# Deploy Configuration

## Required Environment Variables

### For Nova Authentication (Composer)

Для установки Laravel Nova через Composer требуется аутентификация. Добавьте следующие переменные окружения:

**Вариант 1: Использование COMPOSER_AUTH (рекомендуется)**

```bash
COMPOSER_AUTH={"http-basic":{"nova.laravel.com":{"username":"maksstepenko@gmail.com","password":"1XWBgpHKrgUbrWEs4i2dtHPRO3stnWSl0xSNgZOiLAoIUMlKJi"}}}
```

**Вариант 2: Использование отдельных переменных**

Если ваша платформа деплоя поддерживает создание файлов перед установкой, используйте:

```bash
COMPOSER_AUTH_NOVA_USERNAME=maksstepenko@gmail.com
COMPOSER_AUTH_NOVA_PASSWORD=1XWBgpHKrgUbrWEs4i2dtHPRO3stnWSl0xSNgZOiLAoIUMlKJi
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
NOVA_LICENSE_KEY=your-nova-license-key
```

## Deploy Command

```bash
composer install --optimize-autoloader --no-scripts --no-interaction --ignore-platform-req=ext-zip
```

## После деплоя

Выполните миграции и сидер для создания пользователя Nova:

```bash
php artisan migrate --force
php artisan db:seed --force
```

