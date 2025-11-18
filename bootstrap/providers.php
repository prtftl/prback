<?php

$providers = [
    App\Providers\AppServiceProvider::class,
];

// Загружаем Nova только если пакет установлен
if (class_exists(\Laravel\Nova\Nova::class)) {
    $providers[] = App\Providers\NovaServiceProvider::class;
}

return $providers;
