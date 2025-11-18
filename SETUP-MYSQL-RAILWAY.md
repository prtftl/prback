# Создание MySQL базы данных в Railway

## Пошаговая инструкция

### Шаг 1: Создайте MySQL сервис в Railway

1. Откройте ваш проект в Railway
2. Нажмите кнопку **"+ New"** или **"+ Add Service"** (обычно в правом верхнем углу или в списке сервисов)
3. Выберите **"Database"**
4. Выберите **"MySQL"** из списка доступных баз данных
5. Railway автоматически создаст MySQL сервис

### Шаг 2: Railway автоматически добавит переменные окружения

После создания MySQL, Railway автоматически добавит следующие переменные окружения в ваш Laravel сервис:

- `MYSQL_HOST` или `DB_HOST`
- `MYSQL_PORT` или `DB_PORT`
- `MYSQL_DATABASE` или `DB_DATABASE`
- `MYSQL_USER` или `DB_USERNAME`
- `MYSQL_PASSWORD` или `DB_PASSWORD`
- `MYSQL_URL` или `DB_URL` (может содержать полную строку подключения)

### Шаг 3: Настройте переменную DB_CONNECTION

В Railway → Variables добавьте или проверьте:

**Key:** `DB_CONNECTION`  
**Value:** `mysql`

Это нужно, чтобы Laravel использовал MySQL вместо SQLite по умолчанию.

### Шаг 4: Проверьте переменные окружения

Убедитесь, что в Railway Variables есть все необходимые переменные:

```
DB_CONNECTION=mysql
DB_HOST=<автоматически добавлено Railway>
DB_PORT=<автоматически добавлено Railway>
DB_DATABASE=<автоматически добавлено Railway>
DB_USERNAME=<автоматически добавлено Railway>
DB_PASSWORD=<автоматически добавлено Railway>
```

**Примечание:** Railway может использовать префикс `MYSQL_` вместо `DB_`. В этом случае Laravel автоматически подхватит их через `DB_URL` или нужно будет добавить маппинг.

### Шаг 5: Выполните миграции

После создания базы данных и настройки переменных окружения:

1. Откройте Railway Shell для вашего Laravel сервиса
2. Выполните команды:

```bash
# Проверьте подключение к БД
php artisan tinker
# В tinker выполните: DB::connection()->getPdo();

# Выполните миграции
php artisan migrate --force

# Выполните сидер (если нужно)
php artisan db:seed --force
```

### Шаг 6: Проверьте подключение

В Railway Shell выполните:

```bash
# Проверьте статус миграций
php artisan migrate:status

# Проверьте подключение
php artisan db:show
```

## Если Railway использует префикс MYSQL_

Если Railway добавил переменные с префиксом `MYSQL_` вместо `DB_`, вам нужно либо:

### Вариант 1: Использовать DB_URL (рекомендуется)

Railway обычно автоматически создает `MYSQL_URL` или `DATABASE_URL`. Laravel автоматически использует `DB_URL` если он установлен.

Проверьте, есть ли переменная `DATABASE_URL` или `MYSQL_URL` в Railway Variables.

### Вариант 2: Добавить маппинг вручную

Если Railway создал только `MYSQL_*` переменные, добавьте в Railway Variables:

```
DB_CONNECTION=mysql
DB_HOST=${MYSQL_HOST}
DB_PORT=${MYSQL_PORT}
DB_DATABASE=${MYSQL_DATABASE}
DB_USERNAME=${MYSQL_USER}
DB_PASSWORD=${MYSQL_PASSWORD}
```

## Проверка после настройки

1. **Проверьте логи деплоя** - не должно быть ошибок подключения к БД
2. **Проверьте через Shell:**
   ```bash
   php artisan migrate:status
   ```
3. **Проверьте приложение** - должно работать без ошибок

## Troubleshooting

### Ошибка: "SQLSTATE[HY000] [2002] Connection refused"

**Решение:**
- Убедитесь, что MySQL сервис запущен в Railway
- Проверьте, что переменные окружения правильно установлены
- Убедитесь, что `DB_CONNECTION=mysql` установлен

### Ошибка: "Access denied for user"

**Решение:**
- Проверьте, что `DB_USERNAME` и `DB_PASSWORD` правильные
- Railway должен автоматически создать правильные учетные данные

### Ошибка: "Unknown database"

**Решение:**
- Убедитесь, что `DB_DATABASE` установлен правильно
- Railway должен автоматически создать базу данных

## После успешной настройки

После того как MySQL настроена и миграции выполнены:

1. Приложение должно работать
2. Nova должна иметь доступ к базе данных
3. Можно создавать пользователей через Nova или сидеры

