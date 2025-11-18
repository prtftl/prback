<?php

/**
 * Script to setup Composer authentication for Nova repository
 * This script creates auth.json from environment variables
 * 
 * Usage: php scripts/setup-auth.php
 */

$authData = [];

// Check if COMPOSER_AUTH is already set
if (getenv('COMPOSER_AUTH')) {
    echo "COMPOSER_AUTH is already set, skipping auth.json creation\n";
    exit(0);
}

// Check if individual variables are set
$username = getenv('COMPOSER_AUTH_NOVA_USERNAME');
$password = getenv('COMPOSER_AUTH_NOVA_PASSWORD');

if ($username && $password) {
    $authData = [
        'http-basic' => [
            'nova.laravel.com' => [
                'username' => $username,
                'password' => $password,
            ],
        ],
    ];
    
    $authJson = json_encode($authData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    file_put_contents('auth.json', $authJson);
    echo "Composer auth.json created successfully for Nova repository\n";
    exit(0);
}

echo "Warning: No Nova authentication credentials found\n";
echo "Set either COMPOSER_AUTH or COMPOSER_AUTH_NOVA_USERNAME + COMPOSER_AUTH_NOVA_PASSWORD\n";
exit(1);

