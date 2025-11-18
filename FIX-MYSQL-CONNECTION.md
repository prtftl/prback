# Решение ошибки "Connection refused" для MySQL в Railway

## Проблема

Ошибка: `SQLSTATE[HY000] [2002] Connection refused`

Это означает, что Laravel не может подключиться к MySQL базе данных, потому что переменные окружения не настроены правильно.

## Причина

Railway автоматически создает переменные окружения с префиксом `MYSQL_*`:
- `MYSQL_HOST`
- `MYSQL_PORT`
- `MYSQL_DATABASE`
- `MYSQL_USER`
- `MYSQL_PASSWORD`
- `MYSQL_URL` или `DATABASE_URL`

Но Laravel по умолчанию ожидает переменные с префиксом `DB_*`:
- `DB_HOST`
- `DB_PORT`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`
- `DB_URL`

## Решение

### Вариант 1: Использовать DB_URL (рекомендуется)

Railway обычно автоматически создает `MYSQL_URL` или `DATABASE_URL`. Laravel автоматически использует `DB_URL` если он установлен.

**Проверьте в Railway Variables:**
- Есть ли переменная `MYSQL_URL` или `DATABASE_URL`?
- Если есть, добавьте в Railway Variables:
  ```
  DB_URL=${MYSQL_URL}
  ```
  или
  ```
  DB_URL=${DATABASE_URL}
  ```

### Вариант 2: Добавить маппинг переменных

Если Railway создал только `MYSQL_*` переменные, добавьте в Railway Variables:

```
DB_CONNECTION=mysql
DB_HOST=${MYSQL_HOST}
DB_PORT=${MYSQL_PORT}
DB_DATABASE=${MYSQL_DATABASE}
DB_USERNAME=${MYSQL_USER}
DB_PASSWORD=${MYSQL_PASSWORD}
```

**Важно:** Используйте синтаксис `${MYSQL_HOST}` для ссылки на другую переменную.

### Вариант 3: Обновить конфигурацию (уже сделано)

Конфигурация `config/database.php` обновлена, чтобы автоматически использовать переменные с префиксом `MYSQL_*`, если `DB_*` переменные не установлены.

## Пошаговая инструкция

### Шаг 1: Проверьте переменные в Railway

1. Откройте Railway → ваш проект → Variables
2. Найдите переменные, начинающиеся с `MYSQL_`:
   - `MYSQL_HOST`
   - `MYSQL_PORT`
   - `MYSQL_DATABASE`
   - `MYSQL_USER`
   - `MYSQL_PASSWORD`
   - `MYSQL_URL` или `DATABASE_URL`

### Шаг 2: Добавьте DB_CONNECTION

В Railway Variables добавьте:
```
DB_CONNECTION=mysql
```

### Шаг 3: Используйте DB_URL (если доступен)

Если есть `MYSQL_URL` или `DATABASE_URL`, добавьте:
```
DB_URL=${MYSQL_URL}
```
или
```
DB_URL=${DATABASE_URL}
```

### Шаг 4: Или добавьте маппинг переменных

Если `DB_URL` нет, добавьте маппинг:
```
DB_HOST=${MYSQL_HOST}
DB_PORT=${MYSQL_PORT}
DB_DATABASE=${MYSQL_DATABASE}
DB_USERNAME=${MYSQL_USER}
DB_PASSWORD=${MYSQL_PASSWORD}
```

### Шаг 5: Проверьте подключение

После добавления переменных:

1. **Дождитесь нового деплоя** (Railway автоматически перезапустит приложение)
2. **Или выполните в Railway Shell:**
   ```bash
   php artisan config:clear
   php artisan tinker
   # В tinker: DB::connection()->getPdo();
   ```

### Шаг 6: Выполните миграции

После успешного подключения:
```bash
php artisan migrate --force
```

## Проверка

После настройки проверьте:

1. **Логи деплоя** - не должно быть ошибок "Connection refused"
2. **Через Shell:**
   ```bash
   php artisan migrate:status
   ```
3. **Приложение** - должно работать без ошибок

## Важно

- После добавления переменных Railway автоматически перезапустит приложение
- Убедитесь, что MySQL сервис запущен в Railway
- Проверьте, что MySQL сервис и Laravel сервис находятся в одном проекте Railway

