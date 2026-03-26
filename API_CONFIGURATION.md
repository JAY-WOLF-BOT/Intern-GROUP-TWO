# API Configuration Guide

## Overview

This file documents all API configuration settings needed for the Housing Marketplace platform.

## Environment Variables

### Application Settings
```env
APP_NAME="Housing Marketplace"
APP_ENV=local # (local, staging, production)
APP_DEBUG=true # (false in production)
APP_URL=http://localhost:8000
APP_KEY=base64:YOUR_KEY_HERE
APP_TIMEZONE=Africa/Accra
```

### Database Configuration
```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
# OR for MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=housing_marketplace
# DB_USERNAME=root
# DB_PASSWORD=
```

### Authentication
```env
# Session configuration
SESSION_DRIVER=database
SESSION_LIFETIME=120 # minutes

# Sanctum (API tokens)
SANCTUM_STATEFUL_DOMAINS=localhost:3000,127.0.0.1:3000,localhost:8000,127.0.0.1:8000

# CSRF configuration
CSRF_TRUSTED_DOMAINS=.localhost,.127.0.0.1
```

### Mail Configuration
```env
MAIL_DRIVER=log # Use 'log' for development
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@housing-marketplace.local
MAIL_FROM_NAME="Housing Marketplace"
```

### File Storage
```env
FILESYSTEM_DRIVER=local
CLOUDINARY_URL=cloudinary://API_KEY:API_SECRET@CLOUD_NAME
CLOUDINARY_API_KEY=your_api_key
CLOUDINARY_API_SECRET=your_api_secret
CLOUDINARY_CLOUD_NAME=your_cloud_name
```

### Cache Configuration
```env
CACHE_DRIVER=database # or redis for production
CACHE_PREFIX=hm_cache_

# Redis (optional, for performance)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Queue Configuration
```env
QUEUE_CONNECTION=database # or redis
QUEUE_DRIVER=database

# Job timeout (seconds)
QUEUE_TIMEOUT=60
```

## API Rate Limiting

### Configuration (config/api.php)
```php
return [
    'rate_limit' => [
        'enabled' => true,
        'public' => 30,      // 30 requests/minute for public routes
        'authenticated' => 60, // 60 requests/minute for authenticated
        'login_attempts' => 5,
        'otp_requests' => 3,
    ],
];
```

## CORS Configuration (config/cors.php)

```php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'], // Restrict in production
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
```

## Authentication Methods

### 1. Mobile App (Sanctum Token)

**Header:**
```
Authorization: Bearer YOUR_TOKEN_HERE
```

**Obtaining Token:**
```
POST /api/v1/auth/login
{
  "email": "user@example.com",
  "password": "password"
}

Response:
{
  "data": {
    "token": "YOUR_TOKEN_HERE"
  }
}
```

### 2. Web Dashboard (Session + CSRF)

**Cookie:**
```
XSRF-TOKEN: (automatically set by Laravel)
```

**Header:**
```
X-CSRF-TOKEN: VALUE_FROM_META_TAG
```

**Setup in Frontend:**
```html
<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
  // Automatically set in fetch requests
  const csrf = document.querySelector('meta[name="csrf-token"]').content;
  
  fetch('/api/endpoint', {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': csrf,
      'Content-Type': 'application/json'
    }
  });
</script>
```

## API Response Format

### Success Response (200 OK)
```json
{
  "success": true,
  "message": "Operation completed successfully",
  "data": {
    "id": 1,
    "name": "Sample Property"
  },
  "timestamp": "2024-01-15T10:30:00Z"
}
```

### Paginated Response
```json
{
  "success": true,
  "message": "Data retrieved successfully",
  "data": [...],
  "meta": {
    "total": 100,
    "per_page": 15,
    "current_page": 1,
    "last_page": 7,
    "from": 1,
    "to": 15
  },
  "timestamp": "2024-01-15T10:30:00Z"
}
```

### Error Response (4xx/5xx)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["Email is required"],
    "password": ["Password must be at least 8 characters"]
  },
  "timestamp": "2024-01-15T10:30:00Z"
}
```

## Common Headers

**Request:**
```
Content-Type: application/json
Accept: application/json
Accept-Language: en-US
User-Agent: Your App v1.0
```

**Response:**
```
Content-Type: application/json
Cache-Control: no-store
X-Content-Type-Options: nosniff
X-Frame-Options: DENY
```

## Error Handling

### HTTP Status Codes

| Code | Meaning | Example |
|------|---------|---------|
| 200  | OK | Data retrieved successfully |
| 201  | Created | Resource created |
| 204  | No Content | Deletion successful, no response body |
| 400  | Bad Request | Invalid request format |
| 401  | Unauthorized | Missing/invalid authentication |
| 403  | Forbidden | Authenticated but not allowed |
| 404  | Not Found | Resource doesn't exist |
| 422  | Validation Error | Invalid input data |
| 429  | Rate Limited | Too many requests |
| 500  | Server Error | Internal error |

### Error Response Examples

**Validation Error (422):**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["Email must be unique"],
    "phone_number": ["Invalid phone format"]
  }
}
```

**Unauthorized (401):**
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

**Rate Limited (429):**
```json
{
  "success": false,
  "message": "Too many requests. Please try again later."
}
```

## Testing the API

### Using Postman

1. Import the API collection
2. Set up environment variables:
   - `base_url`: http://localhost:8000/api
   - `token`: Retrieved from login endpoint
   - `csrf_token`: Retrieved from login response

### Using cURL

```bash
# Register
curl -X POST http://localhost:8000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "phone_number": "+233501234567",
    "password": "password",
    "password_confirmation": "password",
    "role": "tenant"
  }'

# Login
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password"
  }'

# Get listings
curl -X GET http://localhost:8000/api/v1/listings \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

## Performance & Optimization

### Caching Strategy
- List endpoints: 15-60 minutes
- User profiles: 5 minutes
- Listings: 30 minutes (invalidate on update)

**Implementation:**
```php
$listings = Cache::remember('listings:all', 30, function () {
    return Listing::with(['photos', 'landlord'])->verified()->get();
});
```

### Rate Limiting
- Anonymous: 30 requests/minute
- Authenticated: 60 requests/minute
- Login attempts: 5 per minute
- OTP requests: 3 per minute

### Pagination
- Default page size: 15
- Max page size: 100
- Query: `?page=1&per_page=15`

## Webhook Configuration

### Payment Confirmation Webhook
```
POST /api/v1/payments/confirm
Content-Type: application/json

{
  "payment_id": "PAY_xxx",
  "external_payment_id": "MoMo_xxx",
  "status": "completed",
  "amount": 100.00,
  "timestamp": "2024-01-15T10:30:00Z",
  "signature": "WEBHOOK_SIGNATURE"
}
```

**Webhook Security:**
- Always validate signature
- Verify timestamp (within 5 minutes)
- Store and check request ID to prevent duplicates

## Troubleshooting

### CORS Errors
- Check `config/cors.php`
- Verify `SANCTUM_STATEFUL_DOMAINS` in `.env`
- Ensure credentials are sent with requests

### Authentication Issues
- Verify token expiration (default: 14 days)
- Check `SESSION_LIFETIME` setting
- Ensure CSRF token is current

### Rate Limiting
- Check headers: `X-RateLimit-Remaining`
- Wait according to `Retry-After` header
- Implement exponential backoff

## Deployment Checklist

- [ ] Set `APP_DEBUG=false`
- [ ] Update `APP_URL` to production domain
- [ ] Configure CSRF_TRUSTED_DOMAINS
- [ ] Set `SANCTUM_STATEFUL_DOMAINS` to production domains
- [ ] Configure mail service
- [ ] Update database connection
- [ ] Set up SSL/HTTPS
- [ ] Configure rate limiting
- [ ] Set up caching strategy
- [ ] Enable query logging for optimization
- [ ] Configure backup strategy
- [ ] Set up monitoring/error logging

## References

- [Laravel API Best Practices](https://laravel.com)
- [API_DOCUMENTATION.md](API_DOCUMENTATION.md)
- [TESTING_GUIDE.md](TESTING_GUIDE.md)
