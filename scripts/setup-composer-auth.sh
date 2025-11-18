#!/bin/bash

# Script to setup Composer authentication for Nova repository
# This script creates auth.json from environment variables

if [ -z "$COMPOSER_AUTH_NOVA_USERNAME" ] || [ -z "$COMPOSER_AUTH_NOVA_PASSWORD" ]; then
    echo "Warning: COMPOSER_AUTH_NOVA_USERNAME or COMPOSER_AUTH_NOVA_PASSWORD not set"
    echo "Nova installation may fail without authentication"
    exit 0
fi

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

