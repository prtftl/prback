<?php

$providers = [
    App\Providers\AppServiceProvider::class,
];

// Загружаем Nova только если пакет установлен
// Проверяем наличие основного класса Nova перед загрузкой провайдера
if (class_exists(\Laravel\Nova\Nova::class) && 
    class_exists(\Laravel\Nova\NovaApplicationServiceProvider::class)) {
    $providers[] = App\Providers\NovaServiceProvider::class;
}

return $providers;
