# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.


## About project

Aisello jest to projekt płatnego API.
Umożliwia przeróżne funkcjonalności dla m.in n8n.

## Implementation

Projekt jest napisany zgodnie z najlepszymi praktykami SOLID, KISS, DRY i YAGNI.

Wykorzystuje wzorce projektowe.

## Comments code
Wszystkie elementy w kodzie posiadają PHPdoc, oraz komentarz w języku polskim

## Translation

Każdy komunikat wyświetlany użytkownikowi jest przetłumaczony na język polski i angielski.

Nie można zwracać informacji bezpośrednio, musi ona być przez translacje

## Testing
Wszelkie elemnty na witrynie muszą być otestowane 


## Project Overview

This is a Laravel 12 application using PHP 8.2+ with Vite for frontend asset compilation and Tailwind CSS 4.0 for styling. The project uses SQLite as the default database and includes Laravel Sail for Docker-based development.

## Development Commands

### Initial Setup
```bash
composer run setup
```
This will install dependencies, create .env file, generate app key, run migrations, and build frontend assets.

### Development Server
```bash
composer run dev
```
Runs concurrently:
- PHP development server (port 8000)
- Queue listener
- Laravel Pail (logs viewer)
- Vite dev server (port 5173)

Alternatively, use individual commands:
```bash
php artisan serve              # Start development server
npm run dev                    # Start Vite dev server only
php artisan queue:listen       # Run queue worker
php artisan pail              # View logs in real-time
```

### Docker Development (Laravel Sail)
```bash
sail up          # Start Docker environment (MySQL, Redis)
sail down        # Stop Docker environment
sail artisan     # Run artisan commands in container
sail composer    # Run composer in container
sail npm         # Run npm in container
```

### Testing
```bash
composer run test             # Clear config cache and run PHPUnit tests
./vendor/bin/phpunit          # Run all tests
./vendor/bin/phpunit --filter TestName  # Run specific test
./vendor/bin/phpunit tests/Unit         # Run unit tests only
./vendor/bin/phpunit tests/Feature      # Run feature tests only
```

### Code Quality
```bash
./vendor/bin/pint             # Format code using Laravel Pint
./vendor/bin/pint --test      # Check code formatting without changes
```

### Frontend
```bash
npm run build                 # Build production assets
npm run dev                   # Start Vite development server
```

### Database
```bash
php artisan migrate           # Run migrations
php artisan migrate:fresh     # Drop all tables and re-run migrations
php artisan migrate:fresh --seed  # Fresh migration with seeders
php artisan db:seed           # Run database seeders
php artisan migrate:rollback  # Rollback last migration
php artisan tinker            # Interactive REPL
```

## Architecture

### MVC Structure
- **Models** (`app/Models/`): Eloquent ORM models for database tables
- **Controllers** (`app/Http/Controllers/`): Handle HTTP requests and responses
- **Views** (`resources/views/`): Blade templates for rendering HTML

### Request Lifecycle
1. Entry point: `public/index.php`
2. Bootstrap: `bootstrap/app.php` loads framework and configuration
3. Routes: Defined in `routes/web.php` (web) and `routes/console.php` (CLI)
4. Middleware: HTTP middleware pipeline configured in bootstrap
5. Controllers: Process requests and return responses
6. Views: Blade templates compiled and rendered

### Service Providers
Located in `app/Providers/`, these bootstrap application services:
- `AppServiceProvider.php`: Main application service provider for registering bindings and boot logic

### Frontend Stack
- **Vite**: Module bundler configured in `vite.config.js`
- **Tailwind CSS 4.0**: Utility-first CSS framework with Vite plugin
- **Entry points**:
  - CSS: `resources/css/app.css`
  - JS: `resources/js/app.js`
- **Output**: Compiled to `public/build/`

### Database
- **Default**: SQLite (`database/database.sqlite`)
- **Migrations**: `database/migrations/` - Version control for database schema
- **Seeders**: `database/seeders/` - Populate database with test/initial data
- **Factories**: `database/factories/` - Generate fake data for testing

### Testing
- **PHPUnit**: Test framework configured in `phpunit.xml`
- **Test Environment**: Uses in-memory array cache, sync queues, array mail driver
- **Test Database**: Separate `testing` database to avoid affecting development data
- **Unit Tests**: `tests/Unit/` - Test individual classes/methods in isolation
- **Feature Tests**: `tests/Feature/` - Test HTTP routes and full application features

### Configuration
All configuration files in `config/`:
- `app.php`: Application-wide settings (name, environment, debug mode, locale)
- `database.php`: Database connections (SQLite, MySQL, PostgreSQL, etc.)
- `queue.php`: Queue connections and settings
- `cache.php`: Cache stores configuration
- `mail.php`: Email sending configuration
- `services.php`: Third-party service credentials

Environment variables in `.env` override configuration defaults.

### Storage
- `storage/app/`: Application files (uploads, generated files)
- `storage/framework/`: Framework cache, sessions, views
- `storage/logs/`: Application logs
- **Public Storage**: Link `storage/app/public` to `public/storage` via `php artisan storage:link`

## Key Conventions

### Artisan Commands
Create new components using artisan generators:
```bash
php artisan make:controller NameController
php artisan make:model Name -m          # Model with migration
php artisan make:migration create_table_name
php artisan make:seeder NameSeeder
php artisan make:factory NameFactory
php artisan make:middleware NameMiddleware
php artisan make:request NameRequest    # Form request validation
php artisan make:test NameTest          # Feature test
php artisan make:test NameTest --unit   # Unit test
```

### Autoloading
PSR-4 autoloading configured in `composer.json`:
- `App\` → `app/`
- `Database\Factories\` → `database/factories/`
- `Database\Seeders\` → `database/seeders/`
- `Tests\` → `tests/`

Run `composer dump-autoload` after adding new classes outside of artisan generators.

### Routing
- Web routes in `routes/web.php` automatically have web middleware (sessions, CSRF)
- Route-model binding: Use model type-hints in controller methods for automatic injection
- Resource routes: `Route::resource('name', NameController::class)` creates CRUD routes

### Eloquent ORM
- Models use `snake_case` table names (plural): `User` model → `users` table
- Relationships: Define in model methods (`hasMany`, `belongsTo`, `belongsToMany`, etc.)
- Mass assignment: Protect with `$fillable` or `$guarded` properties
- Timestamps: `created_at` and `updated_at` managed automatically

### Blade Templates
- Layout inheritance: `@extends()`, `@section()`, `@yield()`
- Components: Reusable UI elements in `resources/views/components/`
- Directives: `@if`, `@foreach`, `@auth`, `@csrf`, `@method`, etc.
- Asset inclusion: `@vite(['resources/css/app.css', 'resources/js/app.js'])`
