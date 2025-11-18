# Быстрое решение: Почему Nova не устанавливается

## Главная проблема

В `composer.lock` Nova указана с `source` типа `git` (git@github.com), что требует SSH ключ. Но для установки через HTTP нужен `dist` (zip архив).

## Решение (выберите один вариант)

### Вариант 1: Обновить composer.lock локально (рекомендуется)

**Выполните локально:**
```bash
# Убедиться, что используется dist вместо git
composer config preferred-install dist

# Обновить Nova, чтобы использовать dist
composer update laravel/nova --no-interaction

# Проверить, что в composer.lock теперь dist
grep -A 5 '"laravel/nova"' composer.lock | grep -A 2 '"dist"'
```

**Должно показать:**
```json
"dist": {
    "type": "zip",
    "url": "https://nova.laravel.com/dist/..."
}
```

**Затем:**
1. Закоммитьте обновленный `composer.lock`
2. Запушьте в репозиторий
3. Railway пересоберет проект

---

### Вариант 2: Использовать COMPOSER_AUTH напрямую

**В Railway Variables:**
```
COMPOSER_AUTH={"http-basic":{"nova.laravel.com":{"username":"ваш_email@example.com","password":"ваш_license_key"}}}
```

**Build Command:**
```bash
composer config preferred-install dist && composer install --optimize-autoloader --no-scripts --no-interaction --ignore-platform-req=ext-zip && php artisan nova:install
```

**Это заставит Composer:**
1. Использовать dist вместо git
2. Использовать COMPOSER_AUTH для авторизации
3. Установить Nova

---

### Вариант 3: Проверить и исправить переменные окружения

**В Railway Shell (после деплоя):**
```bash
# 1. Проверить переменные
echo "Username: $COMPOSER_AUTH_NOVA_USERNAME"
echo "Password set: $([ -n "$COMPOSER_AUTH_NOVA_PASSWORD" ] && echo 'YES' || echo 'NO')"

# 2. Проверить auth.json
ls -la auth.json && cat auth.json

# 3. Проверить репозитории
composer config repositories --list

# 4. Попробовать установить вручную
composer config preferred-install dist
composer require laravel/nova:5.7.6 --no-interaction -vvv
```

---

## Проверка в composer.json

**Убедитесь, что есть:**
```json
"config": {
    "preferred-install": "dist"
}
```

В вашем `composer.json` это уже есть (строка 92), но нужно обновить `composer.lock`.

---

## Что делать прямо сейчас

### Шаг 1: Локально обновить composer.lock

```bash
composer config preferred-install dist
composer update laravel/nova --no-interaction
git add composer.lock
git commit -m "Fix: Force Nova to use dist instead of git source"
git push
```

### Шаг 2: Проверить переменные в Railway

1. Railway → Laravel сервис → Variables
2. Убедитесь, что есть:
   - `COMPOSER_AUTH` ИЛИ
   - `COMPOSER_AUTH_NOVA_USERNAME` + `COMPOSER_AUTH_NOVA_PASSWORD`

### Шаг 3: Обновить Build Command

```bash
composer config preferred-install dist && bash scripts/setup-composer-auth.sh && composer install --optimize-autoloader --no-scripts --no-interaction --ignore-platform-req=ext-zip && php artisan nova:install
```

### Шаг 4: Дождаться деплоя и проверить

**В логах должно быть:**
```
Installing laravel/nova (5.7.6)
Package operations: 1 install, 0 updates, 0 removals
```

---

## Диагностика после деплоя

**В Railway Shell:**
```bash
# 1. Проверить, установилась ли Nova
composer show laravel/nova

# 2. Проверить, какой source используется
grep -A 10 '"laravel/nova"' composer.lock | head -15

# 3. Проверить маршруты
php artisan route:list | grep nova
```

---

## Резюме

**Проблема:** Composer пытается использовать git source (требует SSH), а не dist (требует HTTP auth)

**Решение:** 
1. Обновить `composer.lock` локально с `preferred-install dist`
2. Убедиться, что переменные окружения правильно настроены
3. Пересобрать проект в Railway

**Проверка:** После деплоя в логах должна быть строка `Installing laravel/nova (5.7.6)`

