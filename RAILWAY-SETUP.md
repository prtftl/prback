# Настройка Railway для Laravel Nova

## Шаг 1: Добавление переменных окружения в Railway

1. Откройте ваш проект в Railway
2. Перейдите в раздел **Variables** (или **Environment Variables**)
3. Добавьте следующие переменные:

### Вариант 1: Использование COMPOSER_AUTH (рекомендуется)

Добавьте одну переменную:

**Key:** `COMPOSER_AUTH`

**Value:** 
```
{"http-basic":{"nova.laravel.com":{"username":"maksstepenko@gmail.com","password":"#a0420121-c03c-4856-a791-c408d579fdb6"}}}
```

**Важно:** 
- Скопируйте значение точно, без пробелов
- Убедитесь, что все кавычки правильные (двойные кавычки)

### Вариант 2: Использование отдельных переменных

Если Railway не поддерживает JSON в одной переменной, используйте:

**Переменная 1:**
- **Key:** `COMPOSER_AUTH_NOVA_USERNAME`
- **Value:** `maksstepenko@gmail.com`

**Переменная 2:**
- **Key:** `COMPOSER_AUTH_NOVA_PASSWORD`
- **Value:** `#a0420121-c03c-4856-a791-c408d579fdb6`

## Шаг 2: Настройка команды деплоя в Railway

### Если используете COMPOSER_AUTH (Вариант 1):

В настройках деплоя (Deploy Settings) команда должна быть:

```bash
composer install --optimize-autoloader --no-scripts --no-interaction --ignore-platform-req=ext-zip
```

### Если используете отдельные переменные (Вариант 2):

В настройках деплоя добавьте команду:

```bash
php scripts/setup-auth.php && composer install --optimize-autoloader --no-scripts --no-interaction --ignore-platform-req=ext-zip
```

## Шаг 3: Переменные для создания пользователя Nova

Также добавьте переменные для создания администратора Nova:

**Переменная 3:**
- **Key:** `NOVA_USER_EMAIL`
- **Value:** `admin@example.com` (или ваш email)

**Переменная 4:**
- **Key:** `NOVA_USER_PASSWORD`
- **Value:** `ваш-безопасный-пароль`

**Переменная 5:**
- **Key:** `NOVA_USER_NAME`
- **Value:** `Administrator`

**Переменная 6:**
- **Key:** `NOVA_LICENSE_KEY`
- **Value:** `#a0420121-c03c-4856-a791-c408d579fdb6`

## Шаг 4: Настройка команды запуска после деплоя

После успешного деплоя выполните миграции и сидер:

В Railway можно добавить команду в раздел **Deploy** или выполнить вручную через **Shell**:

```bash
php artisan migrate --force
php artisan db:seed --force
```

## Проверка

После добавления переменных:

1. Сохраните изменения
2. Запустите новый деплой
3. Проверьте логи деплоя - не должно быть ошибок аутентификации Nova

## Пример полного списка переменных для Railway:

```
COMPOSER_AUTH={"http-basic":{"nova.laravel.com":{"username":"maksstepenko@gmail.com","password":"#a0420121-c03c-4856-a791-c408d579fdb6"}}}

NOVA_USER_EMAIL=admin@example.com
NOVA_USER_PASSWORD=your-secure-password
NOVA_USER_NAME=Administrator
NOVA_LICENSE_KEY=#a0420121-c03c-4856-a791-c408d579fdb6
```

## Troubleshooting

Если получаете ошибку "Missing or incorrect username / password combination":

1. Проверьте, что переменная `COMPOSER_AUTH` добавлена правильно (без пробелов)
2. Убедитесь, что email и ключ правильные
3. Проверьте логи деплоя в Railway
4. Попробуйте использовать Вариант 2 с отдельными переменными

