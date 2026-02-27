# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 12 application for pharmacy affiliation management (farmacias-afiliacion-imd) for IMD.

**Tech Stack:**
- **Backend:** Laravel 12, PHP 8.4
- **Frontend:** Livewire 4.2, Flux UI 2.12 (component library), Tailwind CSS 4
- **Authentication:** Laravel Fortify 1.35 (web auth), Laravel Sanctum 4.3 (API tokens)
- **Database:** MySQL 8.4 (via Sail)
- **Build Tool:** Vite 7
- **Queue/Cache:** Database-backed
- **Email Testing:** Mailpit

## Branding Guidelines

**IMPORTANT: Always use "IMD" instead of "Instituto Médico Dermatológico"**

When writing code, comments, documentation, or user-facing text, always use the acronym "IMD" rather than the full name "Instituto Médico Dermatológico". This maintains brand consistency across the application.

Examples:
- ✅ "Portal de Clientes IMD"
- ✅ "Bienvenido a IMD"
- ❌ "Portal de Clientes Instituto Médico Dermatológico"
- ❌ "Bienvenido a Instituto Médico Dermatológico"

## ⚠️ IMPORTANT: Laravel Sail Environment

**This project uses Laravel Sail (Docker) for development. ALL commands must be prefixed with `./vendor/bin/sail`.**

Examples:
- ❌ `php artisan migrate`
- ✅ `./vendor/bin/sail artisan migrate`

- ❌ `composer install`
- ✅ `./vendor/bin/sail composer install`

- ❌ `npm run dev`
- ✅ `./vendor/bin/sail npm run dev`

**Tip:** Create a shell alias for convenience: `alias sail='./vendor/bin/sail'`

## Development Commands

### Starting the Environment
```bash
./vendor/bin/sail up -d        # Start all containers in detached mode
./vendor/bin/sail down         # Stop all containers
```

### Quick Setup
```bash
./vendor/bin/sail composer setup
```
This installs dependencies, creates .env from .env.example, generates app key, runs migrations, and builds frontend assets.

### Development Server
```bash
./vendor/bin/sail composer dev
```
Starts a comprehensive development environment with:
- Laravel development server (http://localhost)
- Queue worker with hot reload
- Real-time log viewing (Laravel Pail)
- Vite dev server with HMR

Alternatively, run services individually:
```bash
./vendor/bin/sail artisan serve       # Start development server
./vendor/bin/sail npm run dev         # Start Vite dev server
./vendor/bin/sail artisan queue:listen # Start queue worker
./vendor/bin/sail artisan pail        # View logs in real-time
```

### Testing
```bash
./vendor/bin/sail composer test       # Run full test suite
./vendor/bin/sail artisan test        # Run all tests
./vendor/bin/sail artisan test --filter=TestName  # Run specific test
./vendor/bin/sail artisan test tests/Unit/ExampleTest.php  # Run specific file
```

### Code Quality
```bash
./vendor/bin/sail composer exec pint  # Format code (Laravel Pint)
./vendor/bin/sail composer exec pint -- --test  # Check code style without fixing
```

### Database
```bash
./vendor/bin/sail artisan migrate            # Run migrations
./vendor/bin/sail artisan migrate:fresh      # Drop all tables and re-migrate
./vendor/bin/sail artisan migrate:fresh --seed  # Drop, migrate, and seed
./vendor/bin/sail artisan db:seed            # Run seeders
```

### Frontend
```bash
./vendor/bin/sail npm run build       # Build for production
./vendor/bin/sail npm run dev         # Development mode with HMR
```

## Docker Services (Laravel Sail)

The project runs in Docker via Laravel Sail with the following services:
- **MySQL 8.4**: Primary database (port 3306)
- **Redis**: Cache and session storage (port 6379)
- **Mailpit**: Email testing interface (port 8025 for UI, 1025 for SMTP)

The application is accessible at `http://localhost` when Sail is running.

### Common Sail Commands
```bash
./vendor/bin/sail up           # Start containers (foreground)
./vendor/bin/sail up -d        # Start containers (background)
./vendor/bin/sail down         # Stop containers
./vendor/bin/sail restart      # Restart containers
./vendor/bin/sail ps           # Show running containers
./vendor/bin/sail logs         # View container logs
./vendor/bin/sail shell        # Open bash shell in app container
./vendor/bin/sail root-shell   # Open bash shell as root
```

## Authentication

This project uses a dual authentication system:

### Laravel Fortify (Web Authentication)
Fortify handles all web-based authentication:
- Login, registration, logout
- Password reset and email verification
- Two-factor authentication (2FA) support
- Configuration: `config/fortify.php`
- Actions: `app/Actions/Fortify/`

Key routes provided by Fortify:
- `GET/POST /login` - Login
- `GET/POST /register` - Registration
- `GET/POST /forgot-password` - Password reset
- `POST /logout` - Logout
- See all routes: `./vendor/bin/sail artisan route:list`

### Laravel Sanctum (API Authentication)
Sanctum provides API token authentication for your REST API:
- Issue API tokens to users
- Revoke tokens
- Token abilities/permissions
- Configuration: `config/sanctum.php`

**Creating API tokens:**
```php
$token = $user->createToken('token-name')->plainTextToken;
```

**Protecting API routes:**
```php
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
```

## Livewire & Flux UI

### Livewire 4
Livewire allows you to build reactive, dynamic interfaces using PHP instead of JavaScript.

**Creating components:**
```bash
./vendor/bin/sail artisan make:livewire ComponentName
```

**Component locations:**
- Class: `app/Livewire/ComponentName.php`
- View: `resources/views/livewire/component-name.blade.php`

### Flux UI Components
Flux is the official Livewire component library with pre-built, accessible UI components.

**Common Flux components:**
- `<flux:button>` - Buttons
- `<flux:input>` - Form inputs
- `<flux:card>` - Cards
- `<flux:modal>` - Modals
- `<flux:table>` - Tables
- Full component list: https://fluxui.dev/components

**Important:** This project uses Flux Free. For Flux Pro features, you need a license.

## Architecture

### Standard Laravel Structure
- **app/Http/Controllers**: Controller classes
- **app/Models**: Eloquent models
- **app/Providers**: Service providers
- **routes/web.php**: Web routes
- **routes/console.php**: Console commands
- **resources/views**: Blade templates
- **resources/js**: JavaScript entry points
- **resources/css**: CSS entry points
- **database/migrations**: Database migrations
- **database/factories**: Model factories
- **database/seeders**: Database seeders
- **tests/Feature**: Feature tests
- **tests/Unit**: Unit tests

### Frontend Stack
- **Vite**: Build tool with HMR
- **Tailwind CSS 4**: Utility-first CSS framework with Vite plugin
- Entry points: `resources/css/app.css` and `resources/js/app.js`

### Queue Configuration
The application uses database-backed queues by default. Queue workers are automatically started with `composer dev`.

## Environment Configuration

This project uses Laravel Sail with MySQL for local development. Key settings in `.env`:
- `DB_CONNECTION=mysql` (Sail provides MySQL container)
- `DB_HOST=mysql` (Docker service name)
- `DB_PORT=3306`
- `DB_DATABASE=laravel`
- `QUEUE_CONNECTION=database`
- `SESSION_DRIVER=database`
- `CACHE_STORE=database`
- `REDIS_HOST=redis` (Docker service name)
- `MAIL_MAILER=smtp` with Mailpit
- `MAIL_HOST=mailpit`
- `MAIL_PORT=1025`

## Development Tools

- **Laravel Boost**: Enhanced MCP tools for Laravel development (dev dependency)
  - Install Boost skills: `./vendor/bin/sail artisan boost:install`
  - Available skills: Livewire, Flux UI, Fortify, Pest testing, Tailwind CSS
  - Update skills: `./vendor/bin/sail artisan boost:update`
- **Laravel Pail**: Real-time log viewer - shows logs in terminal with color coding (`./vendor/bin/sail artisan pail`)
- **Laravel Tinker**: REPL for interacting with the application (`./vendor/bin/sail artisan tinker`)
- **Concurrently**: Runs multiple dev services simultaneously in `composer dev`
- **Mailpit**: Email testing UI available at `http://localhost:8025`

## API Development

### Creating API Routes
API routes should be defined in `routes/api.php` (create if it doesn't exist) or add to `routes/web.php` with the `/api` prefix.

Example API controller for token authentication:
```php
// routes/api.php
Route::post('/login', [ApiAuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [ApiAuthController::class, 'logout']);
});
```

### Testing API Endpoints
```bash
# Example login request
curl -X POST http://localhost/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}'

# Authenticated request with token
curl http://localhost/api/user \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

## Testing Notes

PHPUnit is configured with separate test database settings. Tests use:
- Array cache/session drivers
- Sync queue connection
- Array mail driver
- SQLite testing database

## Important Restrictions

### Git Workflow
**IMPORTANT: NEVER create git commits on behalf of the user.** Always wait for explicit user instruction before committing any changes.

### Environment Configuration
**IMPORTANT: NEVER modify the .env file.** The environment configuration is managed by the user. Do not add, remove, or modify any variables in the `.env` file under any circumstances.
