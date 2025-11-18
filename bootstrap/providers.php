<?php

$providers = [
    App\Providers\AppServiceProvider::class,
];

// NovaServiceProvider временно отключен
// Раскомментируйте после установки Nova
// if (class_exists(\Laravel\Nova\Nova::class) && 
//     class_exists(\Laravel\Nova\NovaApplicationServiceProvider::class)) {
//     $providers[] = App\Providers\NovaServiceProvider::class;
// }

return $providers;
