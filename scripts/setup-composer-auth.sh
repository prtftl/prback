#!/bin/bash

# Script to setup Composer authentication for Nova repository
# This script creates auth.json from environment variables

# If COMPOSER_AUTH is already set, use it
if [ -n "$COMPOSER_AUTH" ]; then
    echo "COMPOSER_AUTH is already set, skipping auth.json creation"
    exit 0
fi

# If individual variables are set, create auth.json
if [ -n "$COMPOSER_AUTH_NOVA_USERNAME" ] && [ -n "$COMPOSER_AUTH_NOVA_PASSWORD" ]; then
    # Create auth.json with Nova credentials
    cat > auth.json <<EOF
{
    "http-basic": {
        "nova.laravel.com": {
            "username": "$COMPOSER_AUTH_NOVA_USERNAME",
            "password": "$COMPOSER_AUTH_NOVA_PASSWORD"
        }
    }
}
EOF
    echo "Composer auth.json created successfully for Nova repository"
    exit 0
fi

echo "Warning: No Nova authentication credentials found"
echo "Set either COMPOSER_AUTH or COMPOSER_AUTH_NOVA_USERNAME + COMPOSER_AUTH_NOVA_PASSWORD"
exit 0

