# API Quick Reference

## Base URL
```
http://localhost:8000/api/v1
http://production-domain.com/api/v1
```

## Authentication Headers

### Mobile App (Bearer Token)
```
Authorization: Bearer YOUR_TOKEN_HERE
```

### Web Dashboard (CSRF Token)
```
X-CSRF-TOKEN: TOKEN_FROM_META_TAG
```

## Core Endpoints

### Authentication (Public)
```
POST   /auth/register              - Register new user
POST   /auth/login                 - Login with email
POST   /auth/login-with-otp        - Login with phone OTP
GET    /auth/me                    - Get current user (protected)
POST   /auth/logout                - Logout user (protected)
POST   /auth/refresh               - Refresh token (protected)
```

### User Profile (Protected)
```
GET    /user/profile               - Get profile
PUT    /user/profile               - Update profile
POST   /user/password/change       - Change password
GET    /user/statistics            - Get stats (landlords)
```

### Listings (Mixed)
```
GET    /listings                   - Search listings (public)
GET    /listings/{id}              - Get single listing (public)
GET    /listings/landlord/my-listings - Get my listings (protected)
POST   /listings                   - Create listing (protected)
PUT    /listings/{id}              - Update listing (protected)
DELETE /listings/{id}              - Delete listing (protected)
```

### Payments (Protected)
```
POST   /payments/initiate          - Start payment
GET    /payments/status/{id}       - Check payment status
GET    /payments/history           - Payment history
POST   /payments/confirm           - Webhook callback (auth optional)
```

### Favorites (Protected)
```
POST   /favorites/add              - Add to favorites
DELETE /favorites/{id}             - Remove favorite
GET    /favorites/my-favorites     - List favorites
GET    /favorites/is-favorited/{id} - Check status
POST   /favorites/clear-all        - Clear all favorites
```

### Photos (Protected)
```
POST   /photos/upload/{listingId}  - Upload photo
DELETE /photos/{id}                - Delete photo
```

### OTP (Public)
```
POST   /otp/request                - Request OTP
POST   /otp/verify                 - Verify OTP
```

### Search & Discovery (Public)
```
GET    /search/listings            - Search with filters
GET    /search/neighborhoods       - List neighborhoods
GET    /search/property-types      - List property types
```

### System (Public)
```
GET    /docs/health                - API health check
GET    /docs/endpoints             - Endpoints reference
```

## Common Request Examples

### Register
```bash
curl -X POST http://localhost:8000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "phone_number": "+233501234567",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "tenant"
  }'
```

### Login
```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

### Get Listings
```bash
curl -X GET 'http://localhost:8000/api/v1/listings?page=1&per_page=15' \
  -H "Content-Type: application/json"
```

### Get Listings with Filters
```bash
curl -X GET 'http://localhost:8000/api/v1/listings?page=1&budget_min=100&budget_max=500&bedrooms=2&neighborhood=Accra' \
  -H "Content-Type: application/json"
```

### Create Listing
```bash
curl -X POST http://localhost:8000/api/v1/listings \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Beautiful 2-bed apartment",
    "description": "Spacious and modern apartment",
    "price": 350.00,
    "bedrooms": 2,
    "bathrooms": 1,
    "neighborhood": "Accra",
    "property_type": "Apartment",
    "latitude": 5.6037,
    "longitude": -0.1870
  }'
```

### Add to Favorites
```bash
curl -X POST http://localhost:8000/api/v1/favorites/add \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"listing_id": 1}'
```

### Initiate Payment
```bash
curl -X POST http://localhost:8000/api/v1/payments/initiate \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "listing_id": 1,
    "payment_type": "viewing_fee",
    "payment_method": "momo",
    "momo_network": "MTN",
    "phone_number": "+233501234567"
  }'
```

## Response Format

### Success Response
```json
{
  "success": true,
  "message": "Operation completed successfully",
  "data": { ... },
  "timestamp": "2024-01-15T10:30:00Z"
}
```

### Paginated Response
```json
{
  "success": true,
  "message": "Data retrieved successfully",
  "data": [ ... ],
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

### Error Response
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

## HTTP Status Codes

| Code | Meaning |
|------|---------|
| 200  | OK - Success |
| 201  | Created - Resource created |
| 400  | Bad Request - Invalid input |
| 401  | Unauthorized - Auth required/failed |
| 403  | Forbidden - Not allowed |
| 404  | Not Found - Resource doesn't exist |
| 422  | Validation Error - Invalid data |
| 429  | Too Many Requests - Rate limited |
| 500  | Server Error |

## Query Parameters

### Pagination
```
?page=1          - Page number (default: 1)
?per_page=15     - Items per page (default: 15, max: 100)
```

### Listing Filters
```
?budget_min=100       - Minimum price
?budget_max=500       - Maximum price
?bedrooms=2           - Number of bedrooms
?neighborhood=Accra   - Location
?property_type=House  - Property type
?sort=-price          - Sort field (prefix with - for desc)
```

### Search
```
?query=apartment      - Search term
?sort=-created_at     - Sort by creation date
```

## Rate Limits

| Endpoint | Limit |
|----------|-------|
| Public endpoints | 30 requests/minute |
| Authenticated | 60 requests/minute |
| Login | 5 attempts/minute |
| OTP Request | 3 attempts/minute |
| Photo Upload | 10 uploads/minute |
| Search | 30 searches/minute |

## Error Messages

| Code | Message | Cause |
|------|---------|-------|
| 401 | Unauthorized | Missing or invalid token |
| 403 | Forbidden | No permission for resource |
| 404 | Not Found | Resource doesn't exist |
| 422 | Validation failed | Invalid input data |
| 429 | Too many requests | Rate limited |
| 500 | Internal server error | Server error (check logs) |

## Development Tools

### Health Check
```bash
curl http://localhost:8000/api/v1/docs/health
```

### View Routes
```bash
php artisan route:list | grep v1
```

### Test with Postman
1. Import: `postman/globals/workspace.globals.yaml`
2. Set `base_url`: `http://localhost:8000/api`
3. Set `token`: From login response
4. Use `{{base_url}}` and `{{token}}` in requests

### Debug with Telescope
```
http://localhost:8000/telescope
```

## Common Issues

### 401 Unauthorized
- ✓ Check token is valid
- ✓ Check token hasn't expired (14 days)
- ✓ Check Authorization header format
- ✓ Try refreshing token: `POST /auth/refresh`

### 422 Validation Error
- ✓ Check all required fields are present
- ✓ Validate data types (string, number, etc.)
- ✓ Check field lengths and formats
- ✓ Review error message details

### 429 Rate Limited
- ✓ Wait 1 minute before retrying
- ✓ Check Retry-After header
- ✓ Implement exponential backoff

### CORS Error
- ✓ Check request origin is allowed
- ✓ Verify Content-Type header is set
- ✓ Check browser console for full error

## Tips & Best Practices

1. **Always check response status**: 200-299 = success, 4xx-5xx = error
2. **Use pagination**: Default 15 items, max 100
3. **Cache results**: GET requests can be cached for performance
4. **Use timestamps**: All responses include ISO 8601 timestamps
5. **Handle errors gracefully**: Show user-friendly messages
6. **Test rate limits**: Build in retry logic with backoff
7. **Log API activity**: For debugging and monitoring
8. **Use HTTPS in production**: Encrypt all data in transit
9. **Store tokens securely**: Mobile: Secure storage, Web: HTTP-only cookies
10. **Refresh tokens**: Get new tokens with `/auth/refresh` before expiry

## Useful Commands

```bash
# Install all dependencies
composer install && npm install

# Start development
composer run dev

# Run tests
composer test

# Clear all caches
composer run cache:clear

# Generate IDE helpers
composer ide:generate

# Check code quality
composer analyze

## Files

- **Routes**: `routes/api.php`
- **Controllers**: `app/Http/Controllers/Api/`
- **Models**: `app/Models/`
- **Tests**: `tests/Feature/Api/`
- **Documentation**: `API_DOCUMENTATION.md`
- **Configuration**: `API_CONFIGURATION.md`

---

**Last Updated:** 2024-01-15
**API Version:** v1
**Status:** Production Ready (after testing)
