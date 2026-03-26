# Developer Guide - Housing Marketplace

## Quick Start

### 1. Initial Setup

```bash
# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Install dependencies
composer install
npm install

# Run migrations
php artisan migrate

# Seed database (optional)
php artisan db:seed

# Build assets
npm run build
```

### 2. Start Development Server

**Option A: Using built-in script**
```bash
composer run dev
```

This command will start:
- PHP Development Server (port 8000)
- Queue listener for background jobs
- Pail for real-time logs
- Vite dev server for assets

**Option B: Individual Commands**
```bash
# Terminal 1: PHP Server
php artisan serve

# Terminal 2: Queue Listener
php artisan queue:listen --tries=1 --timeout=0

# Terminal 3: Watch Logs
php artisan pail --timeout=0

# Terminal 4: Vite Asset Bundler
npm run dev
```

## Project Structure

```
├── app/
│   ├── Constants/
│   │   └── AppConstants.php        # Application constants
│   ├── Exceptions/
│   │   └── ApiException.php        # Custom API exceptions
│   ├── Helpers/
│   │   └── ApiHelper.php           # API utility functions
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/                # API controllers
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── ListingController.php
│   │   │   │   ├── UserController.php
│   │   │   │   ├── PaymentController.php
│   │   │   │   ├── FavoriteController.php
│   │   │   │   └── BaseApiController.php (NEW)
│   │   │   ├── AuthController.php
│   │   │   ├── ListingController.php
│   │   │   └── ...
│   │   ├── Middleware/
│   │   └── Resources/
│   ├── Models/
│   │   ├── User.php
│   │   ├── Listing.php
│   │   ├── Payment.php
│   │   ├── Favorite.php
│   │   ├── Message.php
│   │   └── Photo.php
│   └── Providers/
├── config/
│   ├── api.php                     # API configuration
│   ├── cache.php
│   ├── database.php
│   ├── sanctum.php                 # Token authentication
│   └── ...
├── database/
│   ├── migrations/                 # Database schema
│   ├── seeders/                    # Data seeders
│   └── factories/
├── routes/
│   ├── api.php                     # API endpoints (ENHANCED)
│   ├── web.php                     # Web routes
│   └── console.php
├── resources/
│   ├── css/                        # Tailwind CSS
│   ├── js/                         # JavaScript/Vue
│   └── views/                      # Blade templates
├── storage/                        # File storage
├── tests/                          # Unit/Feature tests
├── public/                         # Public assets
│
├── API_CONFIGURATION.md            # API setup guide (NEW)
├── API_DOCUMENTATION.md            # Full API reference
├── DEVELOPMENT.md                  # This file (NEW)
├── composer.json                   # PHP dependencies (UPDATED)
├── package.json                    # Node dependencies
├── phpunit.xml                     # Testing configuration
└── vite.config.js                  # Asset bundler config
```

## API Development

### Adding a New API Endpoint

**1. Create Request Validation Class**

```bash
php artisan make:request StoreListingRequest
```

**File: `app/Http/Requests/StoreListingRequest.php`**
```php
public function authorize(): bool
{
    return $this->user() !== null;
}

public function rules(): array
{
    return [
        'title' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
        'bedrooms' => 'required|integer|min:1',
    ];
}
```

**2. Create Controller Method**

```php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Listing;
use App\Http\Requests\StoreListingRequest;

class ListingController extends BaseApiController
{
    public function store(StoreListingRequest $request)
    {
        try {
            $listing = Listing::create($request->validated());
            
            return $this->successResponse(
                new ListingResource($listing),
                'Listing created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to create listing: ' . $e->getMessage(),
                500
            );
        }
    }
}
```

**3. Add Route**

```php
Route::post('/listings', [ListingController::class, 'store'])
    ->name('listings.store')
    ->middleware('auth:sanctum');
```

**4. Create API Resource (Optional but recommended)**

```bash
php artisan make:resource ListingResource
```

## Testing

### Run All Tests
```bash
composer test
```

### Run Specific Test File
```bash
php artisan test tests/Feature/Api/AuthTest.php
```

### Run with Coverage
```bash
php artisan test --coverage
```

### Example Test

**File: `tests/Feature/Api/AuthTest.php`**
```php
namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    public function test_user_can_register()
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone_number' => '+233501234567',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'tenant',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['success', 'message', 'data']);
    }
}
```

## Code Quality

### Static Analysis

```bash
# Run Larastan analysis
composer analyze

# Run PHP Code Sniffer
./vendor/bin/phpcs app/

# Fix code style issues
composer lint

# Check code style without fixing
composer lint:check
```

### IDE Helper

Generate IDE metadata for better autocomplete:

```bash
composer ide:generate
```

This generates:
- IDE helper for Laravel classes
- Model hints for relationships
- Meta information

## Database

### Migrations

```bash
# Create new migration
php artisan make:migration create_users_table

# Run pending migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Reset to initial state (dev only!)
php artisan migrate:fresh

# Reset with seeding
composer run migrate:fresh
```

### Seeders

```bash
# Create seeder
php artisan make:seeder UserSeeder

# Run seeders
php artisan db:seed

# Run specific seeder
php artisan db:seed --class=UserSeeder
```

### Example Migration

**File: `database/migrations/XXXX_XX_XX_XXXXXX_create_listings_table.php`**
```php
public function up(): void
{
    Schema::create('listings', function (Blueprint $table) {
        $table->id();
        $table->foreignId('landlord_id')->constrained('users');
        $table->string('title');
        $table->text('description');
        $table->decimal('price', 10, 2);
        $table->integer('bedrooms');
        $table->integer('bathrooms');
        $table->string('neighborhood');
        $table->string('verification_status')->default('pending');
        $table->boolean('is_available')->default(true);
        $table->integer('view_count')->default(0);
        $table->timestamps();
        $table->softDeletes();
        
        $table->index(['landlord_id', 'verification_status']);
        $table->fullText(['title', 'description']);
    });
}
```

## Caching

### Cache Operations

```php
use Illuminate\Support\Facades\Cache;

// Remember value for 60 minutes
$listings = Cache::remember('listings', 60, function () {
    return Listing::all();
});

// Forget cache
Cache::forget('listings');

// Clear all cache
Cache::flush();
```

### Cache Configuration

**File: `.env`**
```env
CACHE_DRIVER=database  # or redis for production
```

## Authentication

### Session-based (Web Dashboard)

```php
// Login
Auth::attempt(['email' => $email, 'password' => $password]);

// Check auth
if (Auth::check()) {
    $user = Auth::user();
}

// Logout
Auth::logout();
```

### Token-based (Mobile API - Sanctum)

```php
// Create token
$token = $user->createToken('mobile-app')->plainTextToken;

// Use in requests
$response = Http::withToken($token)->get('/api/v1/listings');

// Revoke all tokens
$user->tokens()->delete();
```

### CSRF (Web Forms)

```html
<form method="POST" action="/api/listings">
    @csrf
    <input type="text" name="title">
</form>
```

```javascript
// JavaScript
const csrf = document.querySelector('meta[name="csrf-token"]').content;

fetch('/api/listings', {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': csrf,
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({title: 'My Property'})
});
```

## Debugging

### Telescope (Request/Query Profiling)

Open browser: `http://localhost:8000/telescope`

Shows:
- HTTP requests
- Database queries
- Cache operations
- Jobs
- Exceptions

### Debugbar

In development, see query details and performance metrics in browser footer.

Enable in code:
```php
\Debugbar::info('Debug message');
\Debugbar::error('Error message');
```

### Pail (Log Streaming)

```bash
php artisan pail --timeout=0
```

Real-time log streaming to terminal.

### Logging

```php
use Illuminate\Support\Facades\Log;

Log::info('User logged in', ['user_id' => $user->id]);
Log::warning('Low disk space');
Log::error('Payment failed', ['error' => $exception->getMessage()]);
```

## Environment Variables

### Development

```env
APP_ENV=local
APP_DEBUG=true
CACHE_DRIVER=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database
MAIL_DRIVER=log
```

### Staging

```env
APP_ENV=staging
APP_DEBUG=false
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
```

### Production

Never commit `.env` to version control. Use:
```bash
cp .env.example .env.prod
# Edit .env.prod with production values
# Deploy with: --env-file=.env.prod
```

## Troubleshooting

### Clear all caches
```bash
composer run cache:clear
```

### Regenerate IDE helpers
```bash
composer ide:generate
```

### Check migrations status
```bash
php artisan migrate:status
```

### Validate routes
```bash
php artisan route:list
```

### Check environment
```bash
php artisan env
```

## Package Documentation

- **Laravel**: https://laravel.com/docs
- **Sanctum**: https://laravel.com/docs/sanctum
- **Telescope**: https://laravel.com/docs/telescope
- **Sanctum**: https://laravel.com/docs/authentication
- **Query Builder**: https://spatie.be/docs/laravel-query-builder
- **Permissions**: https://spatie.be/docs/laravel-permission

## Performance Tips

1. **Use indexes** on frequently queried columns
2. **Eager load relationships** to avoid N+1 queries
3. **Cache expensive operations** (list endpoints, stats)
4. **Paginate large result sets** (default: 15 per page)
5. **Profile queries** with Telescope/Debugbar before optimizing
6. **Use rate limiting** to prevent abuse

## Deployment

See root-level README.md or DEPLOYMENT.md for production setup.
