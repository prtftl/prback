# Исправление ошибки Build Command

## Проблема

Ошибка при выполнении `php artisan optimize:clear` в Build Command:

```
SQLSTATE[HY000] [2002] php_network_getaddresses: getaddrinfo for mysql.railway.internal failed: Name or service not known
```

## Причина

Команда `php artisan optimize:clear` пытается очистить кеш из базы данных, но:
- Build Command выполняется на этапе **сборки** (build time)
- База данных еще **недоступна** на этом этапе
- База данных доступна только когда приложение **запущено** (runtime)

## Решение

### Убрать `optimize:clear` из Build Command

В Railway → Laravel сервис → Settings → Build Command измените на:

```bash
composer install --optimize-autoloader --no-scripts --no-interaction --ignore-platform-req=ext-zip && php artisan nova:install
```

**Уберите:** `&& php artisan optimize:clear`

### Альтернатива: Использовать только файловый кеш

Если нужно очистить кеш, используйте команды, которые не требуют БД:

```bash
composer install --optimize-autoloader --no-scripts --no-interaction --ignore-platform-req=ext-zip && php artisan nova:install && php artisan config:clear && php artisan route:clear && php artisan view:clear
```

Но лучше просто убрать `optimize:clear` - он не критичен на этапе сборки.

## Важно

✅ **Nova уже установилась успешно!** Видно в логах:
- "Nova scaffolding installed successfully."
- "Publishing Nova Assets / Resources"
- "Generating User Resource"

❌ **Ошибка только в очистке кеша** - это не критично, Nova работает!

## После исправления

1. Измените Build Command (уберите `optimize:clear`)
2. Сохраните настройки
3. Railway автоматически запустит новый деплой
4. Деплой должен завершиться успешно
5. Nova будет доступна по адресу `/nova`

## Проверка

После исправления Build Command:

1. **Деплой должен завершиться успешно** (без ошибок)
2. **Откройте `/nova`** - должна открыться страница входа в Nova
3. **Если нужно очистить кеш** - это можно сделать позже, когда приложение запущено

## Резюме

**Проблема:** `optimize:clear` пытается использовать БД во время сборки, когда БД недоступна.

**Решение:** Убрать `optimize:clear` из Build Command.

**Результат:** Nova уже установлена и работает! Просто нужно исправить Build Command, чтобы деплой завершался без ошибок.

