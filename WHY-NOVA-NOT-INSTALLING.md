# Почему Nova не устанавливается: Детальная диагностика

## Проблема

После добавления переменных окружения и обновления Build Command, строка `Installing laravel/nova` все еще не появляется в логах.

## Возможные причины (по приоритету)

### 1. ❌ Переменные окружения не применяются или неправильно настроены

**Проблема:** Railway не видит переменные окружения или они в неправильном формате.

**Проверка в Railway Shell (после деплоя):**
```bash
# Проверить переменные
echo $COMPOSER_AUTH
echo $COMPOSER_AUTH_NOVA_USERNAME
echo $COMPOSER_AUTH_NOVA_PASSWORD
```

**Что должно быть:**
- `COMPOSER_AUTH` → должен показать JSON (не пусто)
- ИЛИ `COMPOSER_AUTH_NOVA_USERNAME` → должен показать email (не пусто)
- И `COMPOSER_AUTH_NOVA_PASSWORD` → должен показать license key (не пусто)

**Если пусто:**
- Переменные не сохранены в Railway
- Или сохранены в неправильном сервисе (не в Laravel сервисе)
- Или есть опечатки в названиях переменных

**Решение:**
1. Проверьте Railway → Laravel сервис → Variables
2. Убедитесь, что переменные добавлены именно в **Laravel сервис**, а не в MySQL
3. Проверьте точное написание: `COMPOSER_AUTH_NOVA_USERNAME` (не `COMPOSER_AUTH_NOVA_USER` или другие варианты)

---

### 2. ❌ Скрипт setup-composer-auth.sh не выполняется или не работает

**Проблема:** Скрипт не создает `auth.json` или создает его неправильно.

**Проверка в логах деплоя:**
Ищите строку:
```
Composer auth.json created successfully for Nova repository
```

**Если этой строки нет:**
- Скрипт не выполняется
- Или переменные не найдены

**Проверка в Railway Shell (после деплоя):**
```bash
# Проверить, создался ли auth.json
ls -la auth.json
cat auth.json
```

**Что должно быть в auth.json:**
```json
{
    "http-basic": {
        "nova.laravel.com": {
            "username": "ваш_email@example.com",
            "password": "ваш_license_key"
        }
    }
}
```

**Если файла нет или он пустой:**
- Скрипт не выполнился
- Или переменные не были доступны во время выполнения

**Решение:**
1. Проверьте Build Command - должен быть `bash scripts/setup-composer-auth.sh` в начале
2. Убедитесь, что скрипт имеет права на выполнение (должен быть исполняемым)
3. Проверьте логи деплоя на наличие ошибок при выполнении скрипта

---

### 3. ❌ Ошибки авторизации, которые не видны в логах

**Проблема:** Composer пытается установить Nova, но получает ошибку авторизации, которая не отображается в логах.

**Проверка в логах деплоя:**
Ищите строки:
- `401 Unauthorized`
- `Authentication required`
- `Could not find a version`
- `Your requirements could not be resolved`

**Проверка в Railway Shell (после деплоя):**
```bash
# Попробовать установить Nova вручную
composer require laravel/nova:5.7.6 --no-interaction -vvv
```

**Флаг `-vvv` покажет подробные логи, включая ошибки авторизации.**

**Что искать в выводе:**
- Ошибки подключения к `nova.laravel.com`
- Ошибки авторизации
- Проблемы с репозиторием

---

### 4. ❌ Composer не видит репозиторий Nova

**Проблема:** Composer не знает о репозитории Nova или он указан неправильно.

**Проверка в Railway Shell:**
```bash
# Проверить репозитории
composer config repositories --list
```

**Должно быть:**
```
[nova.laravel.com] https://nova.laravel.com
```

**Если репозитория нет:**
- Проблема с `composer.json` или `composer.lock`
- Репозиторий не зарегистрирован

**Проверка composer.json:**
```bash
cat composer.json | grep -A 5 "repositories"
```

**Должно быть:**
```json
"repositories": [
    {
        "type": "composer",
        "url": "https://nova.laravel.com"
    }
]
```

---

### 5. ❌ Проблема с composer.lock - Nova указана как git source

**Проблема:** В `composer.lock` Nova может быть указана с `source` типа `git` (git@github.com), что требует SSH ключ, а не HTTP авторизацию.

**Проверка:**
```bash
# В Railway Shell
grep -A 10 '"laravel/nova"' composer.lock
```

**Если видите:**
```json
"source": {
    "type": "git",
    "url": "git@github.com:laravel/nova.git"
}
```

**Проблема:** Composer пытается использовать git source, который требует SSH ключ, а не HTTP auth.

**Решение:**
1. Локально выполните:
   ```bash
   composer config preferred-install dist
   composer update laravel/nova --no-interaction
   ```
2. Это заставит Composer использовать `dist` (zip) вместо `source` (git)
3. Закоммитьте обновленный `composer.lock`
4. Запушьте в репозиторий
5. Railway пересоберет проект

**Альтернатива:** Убедитесь, что в `composer.json` есть:
```json
"config": {
    "preferred-install": "dist"
}
```

Это заставит Composer использовать zip-архивы вместо git репозиториев.

---

### 6. ❌ Nova уже установлена, но не видна в логах

**Проблема:** Nova может быть установлена, но логи не показывают это из-за флага `--no-interaction` или других настроек.

**Проверка в Railway Shell:**
```bash
# Проверить, установлена ли Nova
composer show laravel/nova

# Проверить папку
ls -la vendor/laravel/nova

# Проверить классы
php -r "echo class_exists('Laravel\Nova\Nova') ? 'YES' : 'NO';"
```

**Если Nova установлена:**
- Проблема не в установке, а в чем-то другом
- Возможно, проблема с регистрацией провайдера или маршрутов

---

### 7. ❌ Проблема с кешем Composer

**Проблема:** Composer использует закешированные данные, которые могут быть устаревшими.

**Решение:**
Добавьте в Build Command очистку кеша:
```bash
composer clear-cache && bash scripts/setup-composer-auth.sh && composer install --optimize-autoloader --no-scripts --no-interaction --ignore-platform-req=ext-zip
```

---

### 8. ❌ Неправильный формат COMPOSER_AUTH

**Проблема:** Если используете `COMPOSER_AUTH` напрямую, JSON может быть в неправильном формате.

**Правильный формат:**
```
COMPOSER_AUTH={"http-basic":{"nova.laravel.com":{"username":"email@example.com","password":"license_key"}}}
```

**Важно:**
- Должно быть в одну строку
- Без переносов строк
- Двойные кавычки внутри JSON должны быть экранированы или использованы одинарные кавычки снаружи

**Проверка:**
```bash
# В Railway Shell
echo $COMPOSER_AUTH | jq .
```

Если команда `jq` не установлена, проверьте вручную формат JSON.

---

## Пошаговая диагностика

### Шаг 1: Проверить переменные окружения

**В Railway:**
1. Проект → Laravel сервис → Variables
2. Убедитесь, что переменные есть и правильно названы
3. Проверьте значения (нет ли лишних пробелов, переносов строк)

**В Railway Shell (после деплоя):**
```bash
env | grep COMPOSER
```

### Шаг 2: Проверить выполнение скрипта

**В логах деплоя ищите:**
```
bash scripts/setup-composer-auth.sh
→ Composer auth.json created successfully
```

**В Railway Shell:**
```bash
ls -la auth.json
cat auth.json
```

### Шаг 3: Проверить репозиторий Nova

**В Railway Shell:**
```bash
composer config repositories --list
```

### Шаг 4: Попробовать установить вручную

**В Railway Shell:**
```bash
composer require laravel/nova:5.7.6 --no-interaction -vvv
```

**Смотрите на вывод:**
- Есть ли ошибки?
- Что говорит Composer?
- Пытается ли он подключиться к репозиторию?

### Шаг 5: Проверить логи деплоя полностью

**В Railway:**
1. Откройте последний деплой
2. Просмотрите **ВСЕ логи** от начала до конца
3. Ищите:
   - Ошибки
   - Предупреждения
   - Сообщения о репозиториях
   - Сообщения об авторизации

---

## Рекомендуемое решение

### Вариант 1: Использовать COMPOSER_AUTH напрямую

**В Railway Variables добавьте:**
```
COMPOSER_AUTH={"http-basic":{"nova.laravel.com":{"username":"ваш_email@example.com","password":"ваш_license_key"}}}
```

**Build Command:**
```bash
composer install --optimize-autoloader --no-scripts --no-interaction --ignore-platform-req=ext-zip && php artisan nova:install
```

**Преимущества:**
- Не нужен скрипт setup-composer-auth.sh
- Composer использует переменную напрямую
- Меньше точек отказа

### Вариант 2: Использовать auth.json через скрипт

**В Railway Variables добавьте:**
```
COMPOSER_AUTH_NOVA_USERNAME=ваш_email@example.com
COMPOSER_AUTH_NOVA_PASSWORD=ваш_license_key
```

**Build Command:**
```bash
bash scripts/setup-composer-auth.sh && composer install --optimize-autoloader --no-scripts --no-interaction --ignore-platform-req=ext-zip && php artisan nova:install
```

**Проверка:**
В логах должна быть строка:
```
Composer auth.json created successfully for Nova repository
```

---

## Что проверить в первую очередь

1. ✅ **Переменные окружения** - есть ли они в Railway Variables?
2. ✅ **Build Command** - правильный ли он?
3. ✅ **Логи деплоя** - есть ли ошибки?
4. ✅ **auth.json** - создается ли файл?
5. ✅ **Репозиторий Nova** - видит ли его Composer?

---

## Быстрая проверка после деплоя

**Выполните в Railway Shell:**
```bash
# 1. Проверить переменные
echo "Username: $COMPOSER_AUTH_NOVA_USERNAME"
echo "Password: ${COMPOSER_AUTH_NOVA_PASSWORD:0:10}..." # Показывает только первые 10 символов

# 2. Проверить auth.json
ls -la auth.json && cat auth.json

# 3. Проверить репозитории
composer config repositories --list

# 4. Проверить, установлена ли Nova
composer show laravel/nova

# 5. Попробовать установить вручную с подробными логами
composer require laravel/nova:5.7.6 --no-interaction -vvv 2>&1 | head -50
```

---

## Резюме

**Наиболее вероятные причины:**
1. Переменные окружения не применяются (80%)
2. Скрипт setup-composer-auth.sh не работает (10%)
3. Ошибки авторизации, которые не видны (5%)
4. Проблема с composer.lock или репозиторием (5%)

**Что делать:**
1. Проверить переменные окружения в Railway Shell
2. Проверить создание auth.json
3. Попробовать установить Nova вручную с флагом `-vvv`
4. Проверить полные логи деплоя на наличие ошибок

