# Быстрое решение проблемы Nova (по логам)

## Проблема из логов

В логах видно:
- ✅ `composer install` выполняется
- ✅ `composer/semver` устанавливается
- ❌ **НЕТ** строки `Installing laravel/nova` → Nova не устанавливается
- ❌ **НЕТ** результата `php artisan nova:install` → команда не работает

## Причина

**Nova не устанавливается, потому что нет авторизации для приватного репозитория.**

## Решение (3 шага)

### Шаг 1: Добавить переменные окружения в Railway

1. Откройте Railway → ваш проект → **Laravel сервис**
2. Перейдите на вкладку **"Variables"**
3. Добавьте две переменные:

```
COMPOSER_AUTH_NOVA_USERNAME=ваш_email@example.com
COMPOSER_AUTH_NOVA_PASSWORD=ваш_license_key
```

**Где взять license key:**
- Это ваш Nova license key (обычно длинная строка)
- Или ваш GitHub Personal Access Token для доступа к Nova

### Шаг 2: Обновить Build Command

1. В том же сервисе перейдите на вкладку **"Settings"**
2. Найдите **"Build Command"**
3. Установите команду:

```bash
bash scripts/setup-composer-auth.sh && composer install --optimize-autoloader --no-scripts --no-interaction --ignore-platform-req=ext-zip && php artisan nova:install
```

**Или если используете COMPOSER_AUTH напрямую:**
```bash
composer install --optimize-autoloader --no-scripts --no-interaction --ignore-platform-req=ext-zip && php artisan nova:install
```

### Шаг 3: Дождаться нового деплоя

1. Сохраните настройки
2. Railway автоматически запустит новый деплой
3. Проверьте логи нового деплоя

**В новых логах должно быть:**
```
Installing laravel/nova (5.7.6)
Publishing Nova Assets / Resources
Nova scaffolding installed successfully
```

## Проверка после деплоя

**В Railway Shell выполните:**
```bash
composer show laravel/nova
```

**Должно показать:**
```
name     : laravel/nova
versions : * 5.7.6
```

**Если показывает ошибку "not found"** → проверьте переменные окружения и пересоберите проект.

## Альтернатива: Использовать COMPOSER_AUTH

Вместо двух переменных можно использовать одну:

**В Railway Variables добавьте:**
```
COMPOSER_AUTH={"http-basic":{"nova.laravel.com":{"username":"ваш_email@example.com","password":"ваш_license_key"}}}
```

**Важно:** Значение должно быть в одну строку, без переносов.

---

## Что изменится в логах

### До исправления:
```
composer install
→ Installing composer/semver (3.4.4)
→ (Nova не устанавливается)
php artisan nova:install
→ (нет вывода)
```

### После исправления:
```
bash scripts/setup-composer-auth.sh
→ Composer auth.json created successfully

composer install
→ Installing laravel/nova (5.7.6)  ← ЭТО ПОЯВИТСЯ!
→ Package operations: 1 install, 0 updates, 0 removals

php artisan nova:install
→ Publishing Nova Assets / Resources  ← ЭТО ПОЯВИТСЯ!
→ Nova scaffolding installed successfully  ← ЭТО ПОЯВИТСЯ!
```

---

## Если не помогло

1. Проверьте, что переменные окружения сохранены в Railway
2. Проверьте, что Build Command обновлен
3. Проверьте полные логи деплоя (не только начало)
4. Ищите ошибки типа "401 Unauthorized" или "Authentication required"

---

## Резюме

**Проблема:** Nova не устанавливается из-за отсутствия авторизации  
**Решение:** Добавить `COMPOSER_AUTH_NOVA_USERNAME` и `COMPOSER_AUTH_NOVA_PASSWORD` в Railway Variables  
**Проверка:** После деплоя выполнить `composer show laravel/nova` в Railway Shell

