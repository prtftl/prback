# Инструкция по деплою

## Обязательные шаги перед `composer install`

### Шаг 1: Настройка аутентификации Nova

**ВАЖНО:** Перед выполнением `composer install` необходимо настроить аутентификацию для репозитория Nova.

#### Способ 1: Использование переменной COMPOSER_AUTH (самый простой)

Добавьте переменную окружения:

```
COMPOSER_AUTH={"http-basic":{"nova.laravel.com":{"username":"maksstepenko@gmail.com","password":"#a0420121-c03c-4856-a791-c408d579fdb6"}}}
```

**Важно:** 
- Значение должно быть в одну строку, без пробелов
- Используйте двойные кавычки внутри JSON
- На некоторых платформах может потребоваться экранирование

#### Способ 2: Использование отдельных переменных + скрипт

Добавьте переменные:
```
COMPOSER_AUTH_NOVA_USERNAME=maksstepenko@gmail.com
COMPOSER_AUTH_NOVA_PASSWORD=#a0420121-c03c-4856-a791-c408d579fdb6
```

И выполните перед `composer install`:
```bash
php scripts/setup-auth.php
```

### Шаг 2: Выполнение composer install

```bash
composer install --optimize-autoloader --no-scripts --no-interaction --ignore-platform-req=ext-zip
```

## Пример для разных платформ деплоя

### Railway / Render / Fly.io

В настройках деплоя добавьте команду build:

```bash
php scripts/setup-auth.php && composer install --optimize-autoloader --no-scripts --no-interaction --ignore-platform-req=ext-zip
```

Или используйте переменную `COMPOSER_AUTH` напрямую.

### Docker

В Dockerfile добавьте перед `composer install`:

```dockerfile
RUN php scripts/setup-auth.php
RUN composer install --optimize-autoloader --no-scripts --no-interaction --ignore-platform-req=ext-zip
```

Или используйте переменную окружения:
```dockerfile
ENV COMPOSER_AUTH={"http-basic":{"nova.laravel.com":{"username":"maksstepenko@gmail.com","password":"#a0420121-c03c-4856-a791-c408d579fdb6"}}}
```
```

### GitHub Actions / CI/CD

```yaml
- name: Setup Nova Auth
  run: php scripts/setup-auth.php
  env:
    COMPOSER_AUTH_NOVA_USERNAME: ${{ secrets.NOVA_USERNAME }}
    COMPOSER_AUTH_NOVA_PASSWORD: ${{ secrets.NOVA_PASSWORD }}

- name: Install Dependencies
  run: composer install --optimize-autoloader --no-scripts --no-interaction --ignore-platform-req=ext-zip
```

## Проверка учетных данных

Если получаете ошибку "Missing or incorrect username / password combination":

1. Убедитесь, что email правильный (maksstepenko@gmail.com)
2. Убедитесь, что используется правильный лицензионный ключ (не пароль от аккаунта)
3. Проверьте, что переменные окружения установлены правильно
4. Проверьте, что `auth.json` создается перед `composer install`

## После успешного деплоя

```bash
php artisan migrate --force
php artisan db:seed --force
```

