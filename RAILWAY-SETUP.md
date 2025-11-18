# Настройка Railway для Laravel Nova

## Шаг 1: Добавление переменных окружения в Railway

**ВАЖНО:** Для установки Nova через Composer нужны ТОЛЬКО 2 переменные для аутентификации!

1. Откройте ваш проект в Railway
2. Перейдите в раздел **Variables** (или **Environment Variables**)
3. Добавьте переменные для аутентификации:

### Вариант 1: Использование COMPOSER_AUTH (рекомендуется)

Добавьте **ОДНУ** переменную:

**Key:** `COMPOSER_AUTH`

**Value:** 
```
{"http-basic":{"nova.laravel.com":{"username":"maksstepenko@gmail.com","password":"#a0420121-c03c-4856-a791-c408d579fdb6"}}}
```

**Важно:** 
- Скопируйте значение точно, без пробелов
- Убедитесь, что все кавычки правильные (двойные кавычки)

### Вариант 2: Использование отдельных переменных

Если Railway не поддерживает JSON в одной переменной, используйте **ДВЕ** переменные:

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

## Шаг 3: Переменные для создания пользователя Nova (ОПЦИОНАЛЬНО)

Эти переменные нужны только для автоматического создания администратора Nova после деплоя. Можно добавить позже или создать пользователя вручную.

**Переменная 3 (опционально):**
- **Key:** `NOVA_USER_EMAIL`
- **Value:** `admin@example.com` (или ваш email)

**Переменная 4 (опционально):**
- **Key:** `NOVA_USER_PASSWORD`
- **Value:** `ваш-безопасный-пароль`

**Переменная 5 (опционально):**
- **Key:** `NOVA_USER_NAME`
- **Value:** `Administrator`

**Переменная 6 (опционально):**
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

## Минимальный список переменных для Railway (только для установки Nova):

**Только для установки через Composer - нужны ТОЛЬКО эти:**

### Вариант 1 (одна переменная):
```
COMPOSER_AUTH={"http-basic":{"nova.laravel.com":{"username":"maksstepenko@gmail.com","password":"GTsoRsPwzmB7xEYfipIMDo4o0E3FpGhXwlzfC47fam9733HSAl"}}}
```

### Вариант 2 (две переменные):
```
COMPOSER_AUTH_NOVA_USERNAME=maksstepenko@gmail.com
COMPOSER_AUTH_NOVA_PASSWORD=GTsoRsPwzmB7xEYfipIMDo4o0E3FpGhXwlzfC47fam9733HSAl
```

**Остальные переменные (для создания пользователя) - опционально, можно добавить позже:**
```
NOVA_USER_EMAIL=admin@example.com
NOVA_USER_PASSWORD=your-secure-password
NOVA_USER_NAME=Administrator
NOVA_LICENSE_KEY=GTsoRsPwzmB7xEYfipIMDo4o0E3FpGhXwlzfC47fam9733HSAl
```

## Troubleshooting

Если получаете ошибку "Missing or incorrect username / password combination":

1. Проверьте, что переменная `COMPOSER_AUTH` добавлена правильно (без пробелов)
2. Убедитесь, что email и ключ правильные
3. Проверьте логи деплоя в Railway
4. Попробуйте использовать Вариант 2 с отдельными переменными

