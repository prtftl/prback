# Настройка подключения к MySQL в Railway

## Что видно на скриншоте

Railway автоматически создал следующие переменные для MySQL:

**С подчеркиваниями:**
- `MYSQL_DATABASE` - имя базы данных
- `MYSQL_PUBLIC_URL` - публичный URL (если нужен внешний доступ)
- `MYSQL_ROOT_PASSWORD` - пароль root пользователя
- `MYSQL_URL` - **полная строка подключения (это то, что нужно!)**

**Без подчеркиваний (альтернативный формат):**
- `MYSQLDATABASE` - имя базы данных
- `MYSQLHOST` - хост MySQL
- `MYSQLPASSWORD` - пароль
- `MYSQLPORT` - порт
- `MYSQLUSER` - пользователь

## Решение: Используйте MYSQL_URL

Самый простой способ - использовать `MYSQL_URL`, который содержит всю информацию для подключения.

### Шаг 1: Перейдите в Laravel сервис

1. В Railway откройте ваш **Laravel сервис** (не MySQL сервис)
2. Перейдите в раздел **Variables**

### Шаг 2: Добавьте переменные

В Variables вашего Laravel сервиса добавьте:

**Обязательно:**
```
DB_CONNECTION=mysql
DB_URL=${MYSQL_URL}
```

**Как это сделать:**
1. Нажмите **"+ New Variable"**
2. **Key:** `DB_CONNECTION`
3. **Value:** `mysql`
4. Сохраните

5. Нажмите **"+ New Variable"** снова
6. **Key:** `DB_URL`
7. **Value:** `${MYSQL_URL}` (используйте синтаксис `${MYSQL_URL}` для ссылки на переменную MySQL сервиса)
8. Сохраните

### Шаг 3: Свяжите переменные (если нужно)

Если Railway не позволяет использовать `${MYSQL_URL}` напрямую:

1. В MySQL сервисе найдите переменную `MYSQL_URL`
2. Скопируйте её значение (нажмите на переменную, чтобы увидеть значение)
3. В Laravel сервисе создайте переменную:
   - **Key:** `DB_URL`
   - **Value:** вставьте скопированное значение из `MYSQL_URL`

### Альтернативный способ: Использовать отдельные переменные

Если `MYSQL_URL` не работает, используйте отдельные переменные:

В Laravel сервисе → Variables добавьте:

```
DB_CONNECTION=mysql
DB_HOST=${MYSQLHOST}
DB_PORT=${MYSQLPORT}
DB_DATABASE=${MYSQLDATABASE}
DB_USERNAME=${MYSQLUSER}
DB_PASSWORD=${MYSQLPASSWORD}
```

Или если Railway использует формат с подчеркиваниями:

```
DB_CONNECTION=mysql
DB_HOST=${MYSQL_HOST}
DB_PORT=${MYSQL_PORT}
DB_DATABASE=${MYSQL_DATABASE}
DB_USERNAME=${MYSQL_USER}
DB_PASSWORD=${MYSQL_ROOT_PASSWORD}
```

**Примечание:** Railway может не поддерживать ссылки на переменные других сервисов через `${}`. В этом случае нужно скопировать значения вручную.

## Как скопировать значения переменных

### Способ 1: Через интерфейс Railway

1. В MySQL сервисе → Variables
2. Нажмите на переменную (например, `MYSQL_URL`)
3. Скопируйте значение
4. В Laravel сервисе → Variables создайте новую переменную с этим значением

### Способ 2: Через Railway Shell

1. Откройте Railway Shell для MySQL сервиса
2. Выполните:
   ```bash
   echo $MYSQL_URL
   ```
3. Скопируйте значение
4. В Laravel сервисе → Variables создайте:
   - **Key:** `DB_URL`
   - **Value:** вставьте скопированное значение

## Проверка после настройки

После добавления переменных:

1. **Дождитесь автоматического деплоя** (Railway перезапустит приложение)
2. **Или выполните в Railway Shell Laravel сервиса:**
   ```bash
   php artisan config:clear
   php artisan tinker
   ```
   В tinker:
   ```php
   DB::connection()->getPdo();
   ```
   Должно вернуть объект PDO без ошибок.

3. **Выполните миграции:**
   ```bash
   php artisan migrate --force
   ```

## Важно

- `MYSQL_URL` содержит полную строку подключения в формате: `mysql://user:password@host:port/database`
- Laravel автоматически распарсит эту строку, если установлена переменная `DB_URL`
- Убедитесь, что `DB_CONNECTION=mysql` установлен

## Если ошибка "Connection refused" сохраняется

1. Проверьте, что MySQL сервис запущен (зеленый индикатор)
2. Проверьте, что переменные добавлены в **Laravel сервис**, а не только в MySQL сервис
3. Убедитесь, что оба сервиса находятся в одном проекте Railway
4. Проверьте логи деплоя Laravel сервиса

