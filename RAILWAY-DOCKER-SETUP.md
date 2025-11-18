# Настройка Docker на Railway

## Переход с Railpack на Docker

### Шаг 1: Создать Dockerfile

Dockerfile уже создан в корне проекта. Он включает:
- PHP 8.2
- Установку зависимостей Composer
- Поддержку Nova (с авторизацией)
- Сборку фронтенда (Vite)
- Запуск через `php artisan serve`

### Шаг 2: Настроить Railway для Docker

1. **Откройте Railway → ваш проект**
2. **Откройте Laravel сервис**
3. **Перейдите в Settings**
4. **Найдите раздел "Build & Deploy"**
5. **Измените Source Type:**
   - Выберите **"Dockerfile"** вместо "Railpack" или "Nixpacks"

### Шаг 3: Настроить переменные окружения

**В Railway → Laravel сервис → Variables добавьте:**

#### Обязательные для Nova:
```
COMPOSER_AUTH_NOVA_USERNAME=ваш_email@example.com
COMPOSER_AUTH_NOVA_PASSWORD=ваш_license_key
```

**ИЛИ:**
```
COMPOSER_AUTH={"http-basic":{"nova.laravel.com":{"username":"ваш_email@example.com","password":"ваш_license_key"}}}
```

#### Обязательные для приложения:
```
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:ваш_ключ
APP_URL=https://prback-production.up.railway.app

NOVA_USER_EMAIL=ваш_email@example.com
SESSION_DRIVER=cookie
```

#### Для базы данных (если используется):
```
DB_CONNECTION=pgsql
DB_HOST=ваш_хост
DB_PORT=5432
DB_DATABASE=ваш_база
DB_USERNAME=ваш_пользователь
DB_PASSWORD=ваш_пароль
```

### Шаг 4: Настроить Build Settings (опционально)

**В Railway → Laravel сервис → Settings → Build:**

Если нужно передать build args для Nova auth:

```
COMPOSER_AUTH_NOVA_USERNAME=ваш_email@example.com
COMPOSER_AUTH_NOVA_PASSWORD=ваш_license_key
```

**Примечание:** Railway автоматически передает переменные окружения как build args, если они установлены в Variables.

### Шаг 5: Настроить Start Command

**В Railway → Laravel сервис → Settings → Deploy:**

Start Command должен быть:
```bash
php artisan serve --host=0.0.0.0 --port=${PORT}
```

**Или оставьте пустым** - Dockerfile уже содержит CMD с правильной командой.

### Шаг 6: Деплой

1. **Сохраните настройки**
2. **Railway автоматически обнаружит Dockerfile**
3. **Запустится сборка Docker образа**
4. **После сборки приложение запустится**

## Как работает Dockerfile

### Stage 1: Builder
1. Устанавливает PHP 8.2 и зависимости
2. Устанавливает Composer
3. Создает `auth.json` для Nova (если переменные предоставлены)
4. Устанавливает PHP зависимости через Composer
5. Устанавливает Node.js зависимости
6. Собирает фронтенд (Vite)
7. Устанавливает Nova ассеты
8. Удаляет `auth.json` (безопасность)

### Stage 2: Production
1. Копирует собранное приложение
2. Устанавливает только runtime зависимости
3. Запускает `php artisan serve` на порту из переменной `PORT`

## Проверка после деплоя

### 1. Проверить логи деплоя

**В Railway → Deployments → последний деплой:**

Ищите:
- ✅ `Installing laravel/nova (5.7.6)` - Nova устанавливается
- ✅ `Publishing Nova Assets` - Nova публикуется
- ✅ `Nova scaffolding installed successfully` - Nova установлена
- ✅ `Starting Laravel development server` - Сервер запущен

### 2. Проверить работу приложения

- Откройте `https://prback-production.up.railway.app`
- Откройте `https://prback-production.up.railway.app/nova`

### 3. Проверить в Railway Shell

```bash
# Проверить Nova
composer show laravel/nova

# Проверить маршруты
php artisan route:list | grep nova

# Проверить переменные
env | grep COMPOSER
env | grep NOVA
```

## Преимущества Docker

1. **Полный контроль** над процессом сборки
2. **Кеширование слоев** - быстрее последующие сборки
3. **Воспроизводимость** - одинаково работает везде
4. **Безопасность** - `auth.json` удаляется после установки
5. **Оптимизация** - multi-stage build уменьшает размер образа

## Решение проблем

### Проблема: Nova не устанавливается

**Решение:**
1. Проверьте переменные окружения `COMPOSER_AUTH_NOVA_USERNAME` и `COMPOSER_AUTH_NOVA_PASSWORD`
2. Проверьте логи сборки - должны быть строки об установке Nova
3. Убедитесь, что переменные установлены ДО начала сборки

### Проблема: Ошибка при сборке фронтенда

**Решение:**
- Dockerfile использует `|| true` для npm build, чтобы не падать если фронтенд не нужен
- Если фронтенд критичен, проверьте `package.json` и зависимости

### Проблема: Приложение не запускается

**Решение:**
1. Проверьте переменную `PORT` - Railway устанавливает её автоматически
2. Проверьте `APP_KEY` - должен быть установлен
3. Проверьте логи приложения в Railway

## Откат на Railpack

Если нужно вернуться на Railpack:

1. Railway → Laravel сервис → Settings
2. Измените Source Type обратно на "Railpack" или "Nixpacks"
3. Удалите или переименуйте Dockerfile (если нужно)

## Резюме

**Что изменилось:**
- ✅ Создан Dockerfile для сборки приложения
- ✅ Создан .dockerignore для оптимизации
- ✅ Dockerfile поддерживает Nova с авторизацией
- ✅ Multi-stage build для оптимизации размера

**Что нужно сделать:**
1. Настроить Railway на использование Dockerfile
2. Убедиться, что переменные окружения установлены
3. Задеплоить и проверить работу

