# Что означает ошибка "Connection refused"?

## Ошибка

```
SQLSTATE[HY000] [2002] Connection refused (Connection: mysql, SQL: select exists...)
```

## Что это значит?

Ошибка "Connection refused" означает, что:

1. **Laravel пытается подключиться к MySQL**, но соединение отклоняется
2. **MySQL сервер недоступен** по указанному адресу и порту
3. **Переменные окружения неправильно настроены** (неправильный хост, порт, или они вообще не установлены)

## Почему это происходит?

### Причина 1: MySQL сервис не запущен

MySQL сервис в Railway не запущен или остановлен.

**Решение:**
1. Откройте Railway → ваш проект
2. Проверьте, что MySQL сервис запущен (должен быть зеленый индикатор)
3. Если сервис остановлен, запустите его

### Причина 2: Неправильные переменные окружения

Laravel пытается подключиться к `127.0.0.1:3306` (localhost), но MySQL находится на другом хосте.

**Решение:**
Проверьте в Railway Variables, что установлены правильные переменные:

```
DB_CONNECTION=mysql
DB_HOST=<должен быть хост MySQL из Railway, не 127.0.0.1>
DB_PORT=<должен быть порт MySQL из Railway>
DB_DATABASE=<имя базы данных>
DB_USERNAME=<пользователь>
DB_PASSWORD=<пароль>
```

Или если Railway использует префикс `MYSQL_*`:
- `MYSQL_HOST`
- `MYSQL_PORT`
- `MYSQL_DATABASE`
- `MYSQL_USER`
- `MYSQL_PASSWORD`

### Причина 3: Переменные окружения не установлены

Railway не добавил переменные окружения автоматически, или они были удалены.

**Решение:**
1. Откройте Railway → ваш проект → Variables
2. Проверьте, есть ли переменные с префиксом `MYSQL_*`
3. Если их нет:
   - Убедитесь, что MySQL сервис создан
   - Railway должен автоматически добавить переменные
   - Если не добавил, создайте MySQL сервис заново

### Причина 4: MySQL и Laravel в разных проектах

MySQL сервис и Laravel сервис находятся в разных проектах Railway, поэтому они не могут общаться.

**Решение:**
Убедитесь, что оба сервиса находятся в одном проекте Railway.

## Как исправить?

### Шаг 1: Проверьте MySQL сервис

1. Откройте Railway → ваш проект
2. Найдите MySQL сервис в списке сервисов
3. Убедитесь, что он запущен (зеленый индикатор)

### Шаг 2: Проверьте переменные окружения

В Railway → Variables проверьте наличие:

**Обязательно:**
- `DB_CONNECTION=mysql`

**Должны быть автоматически добавлены Railway:**
- `MYSQL_HOST` или `DB_HOST`
- `MYSQL_PORT` или `DB_PORT`
- `MYSQL_DATABASE` или `DB_DATABASE`
- `MYSQL_USER` или `DB_USERNAME`
- `MYSQL_PASSWORD` или `DB_PASSWORD`
- `MYSQL_URL` или `DATABASE_URL` (если есть)

### Шаг 3: Используйте DB_URL (рекомендуется)

Если есть `MYSQL_URL` или `DATABASE_URL`, добавьте в Railway Variables:

```
DB_URL=${MYSQL_URL}
```

или

```
DB_URL=${DATABASE_URL}
```

Это самый простой способ - Laravel автоматически распарсит строку подключения.

### Шаг 4: Или добавьте маппинг переменных

Если `DB_URL` нет, добавьте в Railway Variables:

```
DB_CONNECTION=mysql
DB_HOST=${MYSQL_HOST}
DB_PORT=${MYSQL_PORT}
DB_DATABASE=${MYSQL_DATABASE}
DB_USERNAME=${MYSQL_USER}
DB_PASSWORD=${MYSQL_PASSWORD}
```

**Важно:** Используйте синтаксис `${MYSQL_HOST}` для ссылки на другую переменную.

### Шаг 5: Очистите кеш

После добавления переменных, в Railway Shell выполните:

```bash
php artisan config:clear
php artisan cache:clear
```

### Шаг 6: Проверьте подключение

В Railway Shell выполните:

```bash
php artisan tinker
```

В tinker выполните:
```php
DB::connection()->getPdo();
```

Должно вернуть объект PDO без ошибок. Если есть ошибка, проверьте переменные окружения.

## Быстрая проверка

Выполните в Railway Shell:

```bash
# Проверьте переменные окружения
env | grep -i mysql
env | grep -i db

# Проверьте подключение
php artisan tinker
# В tinker: DB::connection()->getPdo();
```

## Что должно быть в Railway Variables?

После создания MySQL сервиса, Railway автоматически должен добавить:

```
MYSQL_HOST=containers-us-west-xxx.railway.app
MYSQL_PORT=3306
MYSQL_DATABASE=railway
MYSQL_USER=root
MYSQL_PASSWORD=<автоматически-сгенерированный-пароль>
MYSQL_URL=mysql://root:password@host:port/database
```

И вы должны добавить:

```
DB_CONNECTION=mysql
DB_URL=${MYSQL_URL}  # или использовать маппинг отдельных переменных
```

## Если ничего не помогает

1. **Пересоздайте MySQL сервис:**
   - Удалите существующий MySQL сервис
   - Создайте новый MySQL сервис
   - Railway автоматически добавит переменные

2. **Проверьте логи MySQL сервиса:**
   - Откройте MySQL сервис в Railway
   - Проверьте логи - не должно быть ошибок запуска

3. **Убедитесь, что оба сервиса в одном проекте:**
   - MySQL и Laravel должны быть в одном проекте Railway
   - Иначе они не могут общаться друг с другом

