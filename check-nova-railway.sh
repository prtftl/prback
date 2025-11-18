#!/bin/bash

# Скрипт для проверки установки Nova на Railway
# Выполните этот скрипт в Railway Shell

echo "=========================================="
echo "Проверка установки Nova на Railway"
echo "=========================================="
echo ""

echo "1. Проверка установки пакета Nova:"
echo "-----------------------------------"
composer show laravel/nova 2>&1 | head -5
echo ""

echo "2. Проверка маршрутов Nova:"
echo "-----------------------------------"
php artisan route:list | grep nova | head -5
echo ""

echo "3. Проверка классов Nova:"
echo "-----------------------------------"
php -r "require 'vendor/autoload.php'; echo class_exists('Laravel\Nova\Nova') ? 'Nova classes: ✅ YES' : 'Nova classes: ❌ NO';"
echo ""
echo ""

echo "4. Проверка папки vendor/laravel/nova:"
echo "-----------------------------------"
if [ -d "vendor/laravel/nova" ]; then
    echo "✅ Папка существует"
    ls -la vendor/laravel/nova | head -3
else
    echo "❌ Папка не существует"
fi
echo ""

echo "5. Проверка переменных окружения:"
echo "-----------------------------------"
if [ -n "$COMPOSER_AUTH_NOVA_USERNAME" ]; then
    echo "✅ COMPOSER_AUTH_NOVA_USERNAME: $COMPOSER_AUTH_NOVA_USERNAME"
else
    echo "❌ COMPOSER_AUTH_NOVA_USERNAME: не установлена"
fi

if [ -n "$COMPOSER_AUTH_NOVA_PASSWORD" ]; then
    echo "✅ COMPOSER_AUTH_NOVA_PASSWORD: установлена (скрыта)"
else
    echo "❌ COMPOSER_AUTH_NOVA_PASSWORD: не установлена"
fi

if [ -n "$COMPOSER_AUTH" ]; then
    echo "✅ COMPOSER_AUTH: установлена"
else
    echo "❌ COMPOSER_AUTH: не установлена"
fi
echo ""

echo "6. Проверка auth.json:"
echo "-----------------------------------"
if [ -f "auth.json" ]; then
    echo "✅ auth.json существует"
    cat auth.json
else
    echo "❌ auth.json не существует"
fi
echo ""

echo "=========================================="
echo "Проверка завершена"
echo "=========================================="

