#!/bin/sh
set -e
cd /var/www

if [ ! -f ".env" ] && [ -f ".env.example" ]; then
    echo "Creating .env file from .env.example..."
    cp .env.example .env
fi

if [ -f ".env" ]; then
  if grep -q "^APP_KEY=$" .env || ! grep -q "^APP_KEY=" .env; then
      echo "Generating APP_KEY..."
      php artisan key:generate
  else
      echo "APP_KEY already set."
  fi
fi


if [ "$APP_ENV" = "production" ]; then
    echo "Caching configuration, routes, and views for production..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
else
    echo "Clearing caches for development/local environment..."
    php artisan optimize:clear
fi

echo "Running database migrations..."
php artisan migrate --force

echo "Starting supervisord..."
exec "$@"