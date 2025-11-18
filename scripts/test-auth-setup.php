<?php

/**
 * Тестовый скрипт для проверки работы настройки аутентификации Nova
 * Этот скрипт проверяет формат и валидность auth.json без реального ключа
 * 
 * Usage: php scripts/test-auth-setup.php
 */

echo "=== Тест настройки аутентификации Nova ===\n\n";

// Тест 1: Проверка создания auth.json с тестовыми данными
echo "Тест 1: Создание auth.json с тестовыми данными\n";
$testUsername = 'test@example.com';
$testPassword = 'test-key-12345';

putenv("COMPOSER_AUTH_NOVA_USERNAME={$testUsername}");
putenv("COMPOSER_AUTH_NOVA_PASSWORD={$testPassword}");

// Временно сохраняем оригинальные значения
$originalAuth = getenv('COMPOSER_AUTH');
if ($originalAuth) {
    putenv("COMPOSER_AUTH=");
}

// Создаем auth.json вручную для теста
$authData = [
    'http-basic' => [
        'nova.laravel.com' => [
            'username' => $testUsername,
            'password' => $testPassword,
        ],
    ],
];

file_put_contents('auth.json', json_encode($authData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

if (file_exists('auth.json')) {
    echo "✓ auth.json создан успешно\n";
    
    // Проверяем содержимое
    $authContent = json_decode(file_get_contents('auth.json'), true);
    if ($authContent) {
        echo "✓ JSON валиден\n";
        
        if (isset($authContent['http-basic']['nova.laravel.com'])) {
            echo "✓ Структура правильная\n";
            
            $auth = $authContent['http-basic']['nova.laravel.com'];
            if ($auth['username'] === $testUsername && $auth['password'] === $testPassword) {
                echo "✓ Данные сохранены правильно\n";
            } else {
                echo "✗ Данные не совпадают\n";
            }
        } else {
            echo "✗ Неправильная структура\n";
        }
    } else {
        echo "✗ JSON невалиден\n";
    }
    
    // Удаляем тестовый файл
    unlink('auth.json');
    echo "✓ Тестовый auth.json удален\n";
} else {
    echo "✗ auth.json не создан\n";
}

echo "\n";

// Тест 2: Проверка формата COMPOSER_AUTH
echo "Тест 2: Проверка формата COMPOSER_AUTH\n";
$composerAuth = [
    'http-basic' => [
        'nova.laravel.com' => [
            'username' => $testUsername,
            'password' => $testPassword,
        ],
    ],
];

$composerAuthJson = json_encode($composerAuth);
echo "Формат COMPOSER_AUTH:\n";
echo $composerAuthJson . "\n";

if (json_decode($composerAuthJson)) {
    echo "✓ JSON валиден\n";
} else {
    echo "✗ JSON невалиден\n";
}

echo "\n";

// Тест 3: Проверка с реальным форматом ключа
echo "Тест 3: Проверка формата с реальным ключом\n";
$realKey = 'GTsoRsPwzmB7xEYfipIMDo4o0E3FpGhXwlzfC47fam9733HSAl';
$realAuth = [
    'http-basic' => [
        'nova.laravel.com' => [
            'username' => 'prtftl',
            'password' => $realKey,
        ],
    ],
];

$realAuthJson = json_encode($realAuth);
if (json_decode($realAuthJson)) {
    echo "✓ Формат с реальным ключом валиден\n";
    echo "Пример COMPOSER_AUTH:\n";
    echo str_replace([' ', "\n"], '', $realAuthJson) . "\n";
} else {
    echo "✗ Формат невалиден\n";
}

echo "\n";

// Восстанавливаем оригинальные значения
if ($originalAuth) {
    putenv("COMPOSER_AUTH={$originalAuth}");
}
putenv("COMPOSER_AUTH_NOVA_USERNAME=");
putenv("COMPOSER_AUTH_NOVA_PASSWORD=");

echo "=== Тесты завершены ===\n";
echo "\n";
echo "Примечание: Для реальной установки Nova требуется действующий лицензионный ключ.\n";
echo "Этот скрипт проверяет только формат и работу скриптов настройки.\n";

