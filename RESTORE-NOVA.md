# Восстановление Nova после установки

После установки Nova через Composer, нужно восстановить классы и конфигурацию:

## Шаг 1: Восстановите директорию классов

```bash
mv app/Nova.disabled app/Nova
```

## Шаг 2: Восстановите конфигурацию

```bash
mv config/nova.php.disabled config/nova.php
```

## Шаг 3: Восстановите NovaServiceProvider

В файле `bootstrap/providers.php` раскомментируйте:

```php
if (class_exists(\Laravel\Nova\Nova::class) && 
    class_exists(\Laravel\Nova\NovaApplicationServiceProvider::class)) {
    $providers[] = App\Providers\NovaServiceProvider::class;
}
```

## Шаг 4: Обновите autoload

```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

## Шаг 5: Проверьте работу

```bash
php artisan route:list | grep nova
```

Должны появиться маршруты Nova.

## Примечание

Классы и конфигурация Nova были временно переименованы, чтобы избежать ошибок автозагрузки, когда Nova не установлена. После установки Nova восстановите все файлы.

