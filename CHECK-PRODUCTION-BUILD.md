# Проверка: Production build содержит Nova?

## Проблема

Если деплоится только фронтенд или только API, Nova может не включиться в production build.

## Что проверить в Railway

### Шаг 1: Проверьте тип сервиса

1. Railway → ваш проект
2. Откройте **Laravel сервис**
3. Перейдите на вкладку **"Settings"**

**Проверьте:**
- Это должен быть **Laravel сервис**, а не отдельный фронтенд или API
- Должен деплоиться **полный Laravel проект**

### Шаг 2: Проверьте Build Command

Railway → Laravel сервис → Settings → Build Command:

**Должно быть:**
```bash
composer install --optimize-autoloader --no-scripts --no-interaction --ignore-platform-req=ext-zip && php artisan nova:install
```

**Проверьте:**
- ✅ Есть ли `composer install`?
- ✅ Есть ли `php artisan nova:install`?
- ❌ Нет ли только `npm install` или `npm run build`?

### Шаг 3: Проверьте Start Command

Railway → Laravel сервис → Settings → Start Command:

**Должно быть что-то вроде:**
```bash
php artisan serve --host=0.0.0.0 --port=${PORT}
```

Или если используется другой способ запуска.

**Проверьте:**
- ✅ Запускается ли Laravel приложение?
- ❌ Не запускается ли только фронтенд (npm/vite)?

### Шаг 4: Проверьте логи деплоя

В логах деплоя ищите:

**Если деплоится полное приложение:**
```
Installing dependencies from lock file
Installing laravel/nova (5.7.6)
Publishing Nova Assets / Resources
```

**Если деплоится только фронтенд:**
```
npm install
npm run build
```
→ Nova не будет установлена!

## Решение

### Если деплоится только фронтенд:

1. **Создайте отдельный Laravel сервис** в Railway
2. **Настройте Build Command** для установки зависимостей PHP
3. **Добавьте команду** `php artisan nova:install`

### Если Build Command неправильный:

Измените Build Command на:

```bash
composer install --optimize-autoloader --no-scripts --no-interaction --ignore-platform-req=ext-zip && php artisan nova:install
```

### Если Start Command неправильный:

Убедитесь, что Start Command запускает Laravel:

```bash
php artisan serve --host=0.0.0.0 --port=${PORT}
```

## Проверка в Railway

### Что должно быть в настройках:

**Build Command:**
```bash
composer install --optimize-autoloader --no-scripts --no-interaction --ignore-platform-req=ext-zip && php artisan nova:install
```

**Start Command:**
```bash
php artisan serve --host=0.0.0.0 --port=${PORT}
```

**Или если используется другой способ:**
- Убедитесь, что запускается Laravel приложение
- Не только фронтенд или API

## Диагностика

### Проверка 1: Что деплоится?

В логах деплоя ищите:
- `composer install` → Деплоится Laravel приложение ✅
- `npm install` → Деплоится только фронтенд ❌

### Проверка 2: Устанавливается ли Nova?

В логах деплоя ищите:
- `Installing laravel/nova` → Nova устанавливается ✅
- Нет упоминаний о Nova → Nova не устанавливается ❌

### Проверка 3: Запускается ли Laravel?

В Start Command должно быть:
- `php artisan serve` → Laravel запускается ✅
- `npm run dev` или `vite` → Запускается только фронтенд ❌

## Важно

- ✅ Должен деплоиться **полный Laravel проект**
- ✅ Build Command должен включать `composer install`
- ✅ Build Command должен включать `php artisan nova:install`
- ✅ Start Command должен запускать Laravel приложение

## Резюме

**Проблема:** Если деплоится только фронтенд или только API, Nova не будет включена.

**Решение:** Убедитесь, что:
1. Деплоится полное Laravel приложение
2. Build Command включает установку PHP зависимостей
3. Build Command включает `php artisan nova:install`
4. Start Command запускает Laravel

**Проверьте в Railway:**
- Build Command
- Start Command
- Логи деплоя

