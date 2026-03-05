# Mobile API Documentation (v1)

## Overview

The Accra Housing Marketplace API provides a complete REST interface for the React Native mobile application. All endpoints are prefixed with `/api/v1/` and return JSON responses.

## Authentication

The API uses **Laravel Sanctum** for token-based authentication. 

### Getting a Token

1. Register or login to get a Bearer token
2. Include the token in all authenticated requests:
   ```
   Authorization: Bearer YOUR_TOKEN_HERE
   ```

### Response Format

All responses follow this structure:

```json
{
  "success": true,
  "message": "Optional descriptive message",
  "data": {
    // Response data here
  },
  "meta": {
    // Pagination info for list endpoints
  }
}
```

On error:
```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    // Field-level validation errors
  }
}
```

---

## 🔐 Authentication Endpoints

### 1. Register
Create a new user account.

- **URL:** `POST /api/v1/auth/register`
- **Auth Required:** No
- **Request Body:**
  ```json
  {
    "name": "John Doe",
    "email": "john@example.com",
    "phone_number": "+233501234567",
    "password": "securepassword123",
    "password_confirmation": "securepassword123",
    "role": "tenant"
  }
  ```
- **Response:** (201 Created)
  ```json
  {
    "success": true,
    "message": "User registered successfully",
    "data": {
      "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "phone_number": "+233501234567",
        "role": "tenant"
      },
      "token": "9|abcdefghijklmnop...",
      "token_type": "Bearer"
    }
  }
  ```
- **Role Options:** `tenant`, `landlord`
- **Phone Format:** `+233XXXXXXXXX` or `0XXXXXXXXXX`

---

### 2. Login
Authenticate with email and password.

- **URL:** `POST /api/v1/auth/login`
- **Auth Required:** No
- **Request Body:**
  ```json
  {
    "email": "john@example.com",
    "password": "securepassword123"
  }
  ```
- **Response:** (200 OK)
  ```json
  {
    "success": true,
    "message": "Login successful",
    "data": {
      "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "phone_number": "+233501234567",
        "role": "tenant"
      },
      "token": "9|abcdefghijklmnop...",
      "token_type": "Bearer"
    }
  }
  ```

---

### 3. Login with OTP
Authenticate using phone-based OTP (for Ghana market).

- **URL:** `POST /api/v1/auth/login-with-otp`
- **Auth Required:** No
- **Request Body:**
  ```json
  {
    "phone_number": "+233501234567",
    "otp_code": "123456"
  }
  ```
- **Response:** (200 OK)
  ```json
  {
    "success": true,
    "message": "Login successful",
    "data": {
      "user": {...},
      "token": "9|abcdefghijklmnop...",
      "token_type": "Bearer"
    }
  }
  ```

---

### 4. Get Current User
Get authenticated user's profile.

- **URL:** `GET /api/v1/auth/me`
- **Auth Required:** Yes
- **Response:** (200 OK)
  ```json
  {
    "success": true,
    "data": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "phone_number": "+233501234567",
      "role": "tenant",
      "profile_info": {
        "bio": "Looking for affordable housing",
        "avatar": "https://..."
      },
      "created_at": "2024-01-15T10:30:00Z"
    }
  }
  ```

---

### 5. Logout
Revoke current access token.

- **URL:** `POST /api/v1/auth/logout`
- **Auth Required:** Yes
- **Response:** (200 OK)
  ```json
  {
    "success": true,
    "message": "Logged out successfully"
  }
  ```

---

### 6. Refresh Token
Get a new token without re-logging in.

- **URL:** `POST /api/v1/auth/refresh`
- **Auth Required:** Yes
- **Response:** (200 OK)
  ```json
  {
    "success": true,
    "message": "Token refreshed successfully",
    "data": {
      "token": "10|newabcdefghijklmnop...",
      "token_type": "Bearer"
    }
  }
  ```

---

## 👤 User Endpoints

### 1. Get User Profile
Get authenticated user's profile details.

- **URL:** `GET /api/v1/user/profile`
- **Auth Required:** Yes
- **Response:** (200 OK)
  ```json
  {
    "success": true,
    "data": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "phone_number": "+233501234567",
      "role": "tenant",
      "profile_info": {
        "bio": "Looking for a 2-bedroom apartment",
        "avatar": "https://cloudinary.com/..."
      },
      "created_at": "2024-01-15T10:30:00Z"
    }
  }
  ```

---

### 2. Update User Profile
Update user profile information.

- **URL:** `PUT /api/v1/user/profile`
- **Auth Required:** Yes
- **Request Body:**
  ```json
  {
    "name": "Jane Doe",
    "bio": "Looking for a nice apartment",
    "avatar_url": "https://cloudinary.com/..."
  }
  ```
- **Fields:**
  - `name` - Full name (optional)
  - `email` - Email address (optional, must be unique)
  - `phone_number` - Phone number (optional, must be unique)
  - `bio` - User bio (optional, max 500 chars)
  - `avatar_url` - Avatar URL (optional)
- **Response:** (200 OK) Returns updated UserResource

---

### 3. Change Password
Change user's password.

- **URL:** `POST /api/v1/user/password/change`
- **Auth Required:** Yes
- **Request Body:**
  ```json
  {
    "current_password": "oldpassword123",
    "new_password": "newpassword456",
    "new_password_confirmation": "newpassword456"
  }
  ```
- **Response:** (200 OK)
  ```json
  {
    "success": true,
    "message": "Password changed successfully"
  }
  ```

---

### 4. Get Landlord Statistics
Get dashboard statistics (landlords only).

- **URL:** `GET /api/v1/user/statistics`
- **Auth Required:** Yes
- **Response:** (200 OK)
  ```json
  {
    "success": true,
    "data": {
      "total_listings": 5,
      "verified_listings": 3,
      "pending_listings": 2,
      "total_views": 156,
      "total_revenue": "1250.50",
      "average_price": "650.00"
    }
  }
  ```
- **Note:** Only accessible by landlord role

---

### 5. Request OTP
Request OTP for phone verification or password reset.

- **URL:** `POST /api/v1/otp/request`
- **Auth Required:** No
- **Request Body:**
  ```json
  {
    "phone_number": "+233501234567"
  }
  ```
- **Response:** (200 OK)
  ```json
  {
    "success": true,
    "message": "OTP sent to your phone number",
    "debug_otp": 123456,
    "expires_in_minutes": 10
  }
  ```
- **Note:** `debug_otp` only returned in debug mode

---

### 6. Verify OTP
Verify OTP code.

- **URL:** `POST /api/v1/otp/verify`
- **Auth Required:** No
- **Request Body:**
  ```json
  {
    "phone_number": "+233501234567",
    "otp_code": "123456"
  }
  ```
- **Response:** (200 OK)
  ```json
  {
    "success": true,
    "message": "OTP verified successfully"
  }
  ```

---

## 🏠 Listing Endpoints

### 1. Search & List Listings
Get all verified listings with filtering and pagination.

- **URL:** `GET /api/v1/listings`
- **Auth Required:** No
- **Query Parameters:**
  ```
  ?budget_min=500
  &budget_max=2000
  &neighborhood=Osu
  &bedrooms=2
  &property_type=apartment
  &sort_by=price
  &sort_order=asc
  &page=1
  ```
- **Filter Options:**
  - `budget_min` - Minimum price (GHS)
  - `budget_max` - Maximum price (GHS)
  - `neighborhood` - Neighborhood name
  - `bedrooms` - Number of bedrooms
  - `property_type` - apartment, house, studio, shared_room, bungalow
  - `sort_by` - price, created_at, bedrooms, view_count
  - `sort_order` - asc, desc
  - `page` - Page number (default: 1)

- **Response:** (200 OK)
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "title": "Beautiful 2-Bedroom Apartment in Osu",
        "description": "Modern apartment with AC and WiFi",
        "price": 1500,
        "bedrooms": 2,
        "bathrooms": 1,
        "property_type": "apartment",
        "neighborhood": "Osu",
        "location": {
          "address": "123 Oxford Street, Osu",
          "latitude": 5.5923,
          "longitude": -0.1845
        },
        "verification_status": "verified",
        "is_available": true,
        "view_count": 42,
        "created_at": "2024-01-15T10:30:00Z",
        "photos": [
          {
            "id": 1,
            "url": "https://cloudinary.com/...",
            "is_primary": true,
            "order": 1
          }
        ],
        "landlord": {
          "id": 5,
          "name": "Mr. Johnson",
          "phone_number": "+233501234567",
          "profile_info": {
            "bio": "Professional property manager"
          }
        },
        "whatsapp_link": "https://wa.me/233501234567?text=...",
        "is_favorited": false
      }
    ],
    "meta": {
      "total": 156,
      "per_page": 15,
      "current_page": 1,
      "last_page": 11,
      "total_pages": 11
    }
  }
  ```

---

### 2. Get Single Listing
Get detailed information about a specific listing.

- **URL:** `GET /api/v1/listings/{listingId}`
- **Auth Required:** No
- **Response:** (200 OK) Returns single listing with full details
  - Increments view count automatically
  - Includes all photos, landlord info, WhatsApp link

---

### 3. Create Listing (Landlord)
Create a new listing.

- **URL:** `POST /api/v1/listings`
- **Auth Required:** Yes
- **Request Body:**
  ```json
  {
    "title": "2-Bedroom Apartment in Tema",
    "description": "Spacious apartment with modern facilities",
    "price": 1200,
    "bedrooms": 2,
    "bathrooms": 1,
    "property_type": "apartment",
    "neighborhood": "Tema",
    "location_address": "45 Industrial Area, Tema",
    "location_lat": 5.6037,
    "location_long": -0.0058
  }
  ```
- **Response:** (201 Created)
  ```json
  {
    "success": true,
    "message": "Listing created successfully. Pending admin approval.",
    "data": {
      "id": 2,
      "title": "2-Bedroom Apartment in Tema",
      "price": 1200,
      "verification_status": "pending",
      ...
    }
  }
  ```
- **Note:** New listings start with `verification_status: "pending"`

---

### 4. Update Listing (Owner)
Update your own listing.

- **URL:** `PUT /api/v1/listings/{listingId}`
- **Auth Required:** Yes
- **Request Body:**
  ```json
  {
    "title": "Updated Title",
    "price": 1300,
    "is_available": true
  }
  ```
- **Response:** (200 OK) Returns updated listing

---

### 5. Delete Listing (Owner)
Delete your own listing.

- **URL:** `DELETE /api/v1/listings/{listingId}`
- **Auth Required:** Yes
- **Response:** (200 OK)
  ```json
  {
    "success": true,
    "message": "Listing deleted successfully"
  }
  ```

---

### 6. Get My Listings (Landlord)
Get all listings created by authenticated landlord.

- **URL:** `GET /api/v1/listings/landlord/my-listings`
- **Auth Required:** Yes
- **Query Parameters:**
  ```
  ?page=1
  ```
- **Response:** (200 OK)
  ```json
  {
    "success": true,
    "data": [
      {
        // Listing objects
      }
    ],
    "meta": {
      "total": 5,
      "per_page": 10,
      "current_page": 1
    }
  }
  ```

---

## 💰 Payment Endpoints

### 1. Initiate Payment
Start a payment for viewing fee or deposit holding.

- **URL:** `POST /api/v1/payments/initiate`
- **Auth Required:** Yes
- **Request Body:**
  ```json
  {
    "listing_id": 1,
    "payment_type": "viewing_fee",
    "payment_method": "momo",
    "momo_network": "MTN",
    "phone_number": "+233501234567"
  }
  ```
- **Payment Types:**
  - `viewing_fee` - 20 GHS to view listing details
  - `deposit_holding` - 10% of listing price to hold property
- **MoMo Networks:** MTN, Vodafone, AirtelTigo
- **Response:** (201 Created)
  ```json
  {
    "success": true,
    "message": "Payment initiated successfully. Please complete the MoMo transaction.",
    "data": {
      "payment": {
        "id": 1,
        "payment_id": "PAY-1703089000-1",
        "amount": 20,
        "payment_type": "viewing_fee",
        "payment_status": "pending",
        "created_at": "2024-01-15T10:30:00Z"
      },
      "instructions": {
        "network": "MTN",
        "amount": "20.00",
        "ussd_code": "*170#",
        "description": "Pay PAY-1703089000-1 to Accra Housing"
      }
    }
  }
  ```

---

### 2. Check Payment Status
Check the status of a payment.

- **URL:** `GET /api/v1/payments/status/{paymentId}`
- **Auth Required:** Yes
- **Response:** (200 OK)
  ```json
  {
    "success": true,
    "data": {
      "id": 1,
      "payment_id": "PAY-1703089000-1",
      "transaction_id": "TXN-...",
      "amount": 20,
      "payment_type": "viewing_fee",
      "payment_status": "completed",
      "paid_at": "2024-01-15T10:35:00Z",
      "listing": {
        "id": 1,
        "title": "Beautiful Apartment",
        "price": 1500
      }
    }
  }
  ```

---

### 3. Get Payment History
Get all payments for authenticated user.

- **URL:** `GET /api/v1/payments/history`
- **Auth Required:** Yes
- **Query Parameters:**
  ```
  ?status=completed
  &type=viewing_fee
  &from_date=2024-01-01
  &to_date=2024-01-31
  &page=1
  ```
- **Response:** (200 OK)
  ```json
  {
    "success": true,
    "data": [
      {
        // Payment objects
      }
    ],
    "meta": {
      "total": 15,
      "per_page": 20,
      "current_page": 1,
      "total_spent": "500.00"
    }
  }
  ```

---

### 4. Confirm Payment (Webhook)
Confirm payment from MoMo provider (internal use).

- **URL:** `POST /api/v1/payments/confirm`
- **Auth Required:** No
- **Request Body:**
  ```json
  {
    "payment_id": "PAY-1703089000-1",
    "transaction_id": "TXN-2024-12345",
    "status": "completed",
    "amount_paid": 20
  }
  ```
- **Response:** (200 OK)
  ```json
  {
    "success": true,
    "message": "Payment confirmed successfully",
    "data": {
      // Updated payment object
    }
  }
  ```

---

## ❤️ Favorite Endpoints

### 1. Add to Favorites
Add a listing to favorites.

- **URL:** `POST /api/v1/favorites/add`
- **Auth Required:** Yes
- **Request Body:**
  ```json
  {
    "listing_id": 1
  }
  ```
- **Response:** (201 Created)
  ```json
  {
    "success": true,
    "message": "Listing added to favorites",
    "data": {
      "favorite_id": 1,
      "listing": {
        // Full listing object
      }
    }
  }
  ```

---

### 2. Remove from Favorites
Remove a listing from favorites.

- **URL:** `DELETE /api/v1/favorites/{favoriteId}`
- **Auth Required:** Yes
- **Response:** (200 OK)
  ```json
  {
    "success": true,
    "message": "Listing removed from favorites"
  }
  ```

---

### 3. Get My Favorites
Get all favorited listings.

- **URL:** `GET /api/v1/favorites/my-favorites`
- **Auth Required:** Yes
- **Query Parameters:**
  ```
  ?neighborhood=Osu
  &sort_by=price
  &sort_order=asc
  &page=1
  ```
- **Response:** (200 OK)
  ```json
  {
    "success": true,
    "data": [
      {
        // Listing objects marked as favorited
      }
    ],
    "meta": {
      "total": 8,
      "per_page": 12,
      "current_page": 1
    }
  }
  ```

---

### 4. Check if Favorited
Check if a specific listing is in user's favorites.

- **URL:** `GET /api/v1/favorites/is-favorited/{listingId}`
- **Auth Required:** No
- **Response:** (200 OK)
  ```json
  {
    "success": true,
    "is_favorited": true
  }
  ```

---

### 5. Clear All Favorites
Delete all favorites at once.

- **URL:** `POST /api/v1/favorites/clear-all`
- **Auth Required:** Yes
- **Response:** (200 OK)
  ```json
  {
    "success": true,
    "message": "Cleared 8 favorite(s)"
  }
  ```

---

## 🔍 Listing Field Reference

Each listing response includes:

| Field | Type | Description |
|-------|------|-------------|
| id | integer | Unique listing ID |
| title | string | Listing title |
| description | string | Full description |
| price | float | Monthly price in GHS |
| bedrooms | integer | Number of bedrooms |
| bathrooms | integer | Number of bathrooms |
| property_type | string | apartment, house, studio, shared_room, bungalow |
| neighborhood | string | Accra neighborhood |
| location | object | { address, latitude, longitude } |
| verification_status | string | pending, verified, rejected |
| is_available | boolean | Listing availability |
| view_count | integer | Number of views |
| photos | array | PhotoResource array |
| landlord | object | UserResource of landlord |
| whatsapp_link | string | WhatsApp deep link to contact landlord |
| is_favorited | boolean | Whether user has favorited this listing |
| created_at | string | ISO 8601 timestamp |
| updated_at | string | ISO 8601 timestamp |

---

## 🏢 User Field Reference

Each user response includes:

| Field | Type | Description |
|-------|------|-------------|
| id | integer | Unique user ID |
| name | string | User's full name |
| email | string | Email address |
| phone_number | string | Phone number in Ghana format |
| role | string | tenant or landlord |
| profile_info | object | { bio, avatar } |
| created_at | string | ISO 8601 timestamp |

---

## 🏤 Photo Field Reference

Each photo response includes:

| Field | Type | Description |
|-------|------|-------------|
| id | integer | Unique photo ID |
| url | string | Cloudinary URL |
| is_primary | boolean | If primary listing photo |
| order | integer | Display order |

---

## ⚠️ Error Handling

### Common HTTP Status Codes

| Code | Meaning |
|------|---------|
| 200 | OK - Request successful |
| 201 | Created - Resource created |
| 400 | Bad Request - Invalid parameters |
| 401 | Unauthorized - Missing/invalid token |
| 403 | Forbidden - Permission denied |
| 404 | Not Found - Resource doesn't exist |
| 422 | Unprocessable Entity - Validation failed |
| 500 | Server Error - Internal error |

### Example Error Response

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email has already been taken"],
    "phone_number": ["The phone number format is invalid"]
  }
}
```

---

## 📍 Accra Neighborhoods

Supported neighborhoods for filtering:
- Osu
- East Legon
- Tema
- Accra Central
- Korle Bu
- Teshie
- Labadi
- Jamestown
- Ashaiman
- Kwabenya

---

## 🔗 Environment Setup

### Base URL
```
https://your-domain.com/api/v1
```

### Example Request (JavaScript/React Native)
```javascript
import axios from 'axios';

const apiClient = axios.create({
  baseURL: 'https://your-domain.com/api/v1',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

// Add token to requests
apiClient.interceptors.request.use((config) => {
  const token = await AsyncStorage.getItem('authToken');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Usage
const listings = await apiClient.get('/listings?neighborhood=Osu');
const response = await apiClient.post('/auth/login', {
  email: 'user@example.com',
  password: 'password'
});
```

---

## 📞 Support

For API issues or questions:
- Email: support@accrahousings.com
- WhatsApp: +233 50 XXX XXXX

---

**Last Updated:** January 2024
**API Version:** v1
