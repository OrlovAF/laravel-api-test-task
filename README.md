## Project stack
- PHP 8.2
- Laravel 11
- SQLite

## Local quickstart
- `cp .env.example .env`
- `composer install`
- `php artisan key:generate`
- `php artisan migrate`
- `php artisan serve`

## Queues processing
- `php artisan queue:work`

## API endpoints
- POST `http://127.0.0.1:8000/api/submit` (body: {"name": "", "email": "", "message": ""})
