# Documentation

## Requirment tools

Requirement

- PHP 8.2
- Composer 2.2.23
- MySQL

## How To Run This Project

```
composer install
cp .env.example .env
npm install && npm run build
composer run dev
php artisan key:generate
php artisan migrate
php artisan db:seed
```

## Testing
- php artisan test

