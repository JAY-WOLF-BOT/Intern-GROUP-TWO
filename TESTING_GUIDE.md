# Mobile API Testing Guide

## Quick Start - Test All Endpoints

This guide will help you test the complete mobile API using Postman or Insomnia.

---

## 1. Environment Setup

### Create Postman Environment Variables

```
{
  "base_url": "http://localhost:8000/api/v1",
  "auth_token": "",
  "user_id": 1,
  "listing_id": 1,
  "favorite_id": 1,
  "payment_id": "PAY-..."
}
```

### Update `base_url` after `{{base_url}}`
```
http://localhost:8000/api/v1
https://your-production-domain.com/api/v1
```

---

## 2. Authentication Flow (Complete Test Sequence)

### Step 1: Register New User
**POST** `{{base_url}}/auth/register`

**Body (JSON):**
```json
{
  "name": "Test Tenant",
  "email": "tenant@test.com",
  "phone_number": "+233501234567",
  "password": "TestPass123",
  "password_confirmation": "TestPass123",
  "role": "tenant"
}
```

**Expected Response (201):**
- Copy `token` value from response
- Paste into Postman: `Auth` → `Bearer Token` → `{{auth_token}}`

**Postman Script (Tests tab):**
```javascript
if (pm.response.code === 201) {
  pm.environment.set("auth_token", pm.response.json().data.token);
}
```

---

### Step 2: Login
**POST** `{{base_url}}/auth/login`

**Body (JSON):**
```json
{
  "email": "tenant@test.com",
  "password": "TestPass123"
}
```

**Expected Response (200):**
- Returns user profile + new token
- Update `auth_token` if testing token rotation

---

### Step 3: Get Current User
**GET** `{{base_url}}/auth/me`

**Headers (automatically added if using Bearer Token):**
```
Authorization: Bearer {{auth_token}}
```

**Expected Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Test Tenant",
    "email": "tenant@test.com",
    ...
  }
}
```

---

### Step 4: Refresh Token
**POST** `{{base_url}}/auth/refresh`

**Headers:** Bearer Token

**Expected Response (200):**
- Returns new token
- Old token still valid until used in logout

---

### Step 5: Logout
**POST** `{{base_url}}/auth/logout`

**Headers:** Bearer Token

**Expected Response (200):**
```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

**Verify:** Try calling `/auth/me` again - should return 401 Unauthorized

---

## 3. Listing Operations

### Search Listings (No Auth Required)
**GET** `{{base_url}}/listings`

**Query Params (try variations):**
```
?budget_min=500&budget_max=2000&neighborhood=Osu&bedrooms=2&sort_by=price&sort_order=asc&page=1
```

**Expected Response (200):**
- Array of listings with pagination metadata

---

### Get Single Listing (No Auth Required)
**GET** `{{base_url}}/listings/1`

**Expected Response (200):**
- View count should increment each call
- Returns ListingResource with photos, landlord info

---

### Create Listing (Landlord - Auth Required)
First, register/login as landlord:

**POST** `{{base_url}}/auth/register`
```json
{
  "name": "Test Landlord",
  "email": "landlord@test.com",
  "phone_number": "+233501234568",
  "password": "TestPass123",
  "password_confirmation": "TestPass123",
  "role": "landlord"
}
```

Then create listing:

**POST** `{{base_url}}/listings`

**Headers:** Bearer Token

**Body:**
```json
{
  "title": "Beautiful 2-Bedroom Apartment",
  "description": "Modern apartment with AC and WiFi",
  "price": 1500,
  "bedrooms": 2,
  "bathrooms": 1,
  "property_type": "apartment",
  "neighborhood": "Osu",
  "location_address": "123 Oxford Street, Osu",
  "location_lat": 5.5923,
  "location_long": -0.1845
}
```

**Expected Response (201):**
- Returns created listing with ID
- Status: "pending" (waiting admin approval)
- Save listing ID: `{{listing_id}}`

---

### Update Listing
**PUT** `{{base_url}}/listings/{{listing_id}}`

**Headers:** Bearer Token

**Body:**
```json
{
  "title": "Updated Title",
  "price": 1600,
  "is_available": true
}
```

**Expected Response (200):**
- Only list owner can update
- Returns updated listing

---

### Get My Listings (Landlord)
**GET** `{{base_url}}/listings/landlord/my-listings`

**Headers:** Bearer Token

**Expected Response (200):**
- All listings created by authenticated landlord
- Includes pending and verified listings

---

### Delete Listing
**DELETE** `{{base_url}}/listings/{{listing_id}}`

**Headers:** Bearer Token

**Expected Response (200):**
```json
{
  "success": true,
  "message": "Listing deleted successfully"
}
```

---

## 4. Favorites Flow

### Add to Favorites
**POST** `{{base_url}}/favorites/add`

**Headers:** Bearer Token

**Body:**
```json
{
  "listing_id": 1
}
```

**Expected Response (201):**
- Returns `favorite_id`
- Save it: `{{favorite_id}}`

---

### Check if Favorited (No Auth for check)
**GET** `{{base_url}}/favorites/is-favorited/1`

**Expected Response (200):**
```json
{
  "success": true,
  "is_favorited": true
}
```

---

### Get My Favorites
**GET** `{{base_url}}/favorites/my-favorites`

**Headers:** Bearer Token

**Query Params:**
```
?neighborhood=Osu&sort_by=price&sort_order=asc&page=1
```

**Expected Response (200):**
- Array of favorited listings

---

### Remove from Favorites
**DELETE** `{{base_url}}/favorites/{{favorite_id}}`

**Headers:** Bearer Token

**Expected Response (200):**
```json
{
  "success": true,
  "message": "Listing removed from favorites"
}
```

---

### Clear All Favorites
**POST** `{{base_url}}/favorites/clear-all`

**Headers:** Bearer Token

**Expected Response (200):**
```json
{
  "success": true,
  "message": "Cleared 5 favorite(s)"
}
```

---

## 5. Payment Operations

### Initiate Payment (Viewing Fee)
**POST** `{{base_url}}/payments/initiate`

**Headers:** Bearer Token

**Body:**
```json
{
  "listing_id": 1,
  "payment_type": "viewing_fee",
  "payment_method": "momo",
  "momo_network": "MTN",
  "phone_number": "+233501234567"
}
```

**Expected Response (201):**
```json
{
  "success": true,
  "data": {
    "payment": {
      "id": 1,
      "payment_id": "PAY-1703089000-1",
      "amount": 20,
      "payment_status": "pending"
    },
    "instructions": {
      "network": "MTN",
      "ussd_code": "*170#",
      ...
    }
  }
}
```

**Save:** `{{payment_id}}` from response

---

### Check Payment Status
**GET** `{{base_url}}/payments/status/PAY-1703089000-1`

**Headers:** Bearer Token

**Expected Response (200):**
- Returns payment details
- Status: pending, completed, failed, cancelled

---

### Initiate Deposit Holding
**POST** `{{base_url}}/payments/initiate`

**Body:**
```json
{
  "listing_id": 1,
  "payment_type": "deposit_holding",
  "payment_method": "momo",
  "momo_network": "Vodafone",
  "phone_number": "+233501234567"
}
```

**Expected Response (201):**
- Amount = 10% of listing price
- Status: pending

---

### Get Payment History
**GET** `{{base_url}}/payments/history`

**Headers:** Bearer Token

**Query Params:**
```
?status=completed&type=viewing_fee&from_date=2024-01-01&to_date=2024-01-31&page=1
```

**Expected Response (200):**
- Array of payments
- Meta includes `total_spent` for completed payments

---

## 6. User Profile Operations

### Get Profile
**GET** `{{base_url}}/user/profile`

**Headers:** Bearer Token

**Expected Response (200):**
- Full user profile

---

### Update Profile
**PUT** `{{base_url}}/user/profile`

**Headers:** Bearer Token

**Body:**
```json
{
  "name": "Updated Name",
  "bio": "Looking for a nice 2-bedroom apartment",
  "avatar_url": "https://example.com/avatar.jpg"
}
```

**Expected Response (200):**
- Returns updated user

---

### Change Password
**POST** `{{base_url}}/user/password/change`

**Headers:** Bearer Token

**Body:**
```json
{
  "current_password": "TestPass123",
  "new_password": "NewPass456",
  "new_password_confirmation": "NewPass456"
}
```

**Expected Response (200):**
```json
{
  "success": true,
  "message": "Password changed successfully"
}
```

---

### Get Landlord Statistics
**GET** `{{base_url}}/user/statistics`

**Headers:** Bearer Token (Landlord account only)

**Expected Response (200):**
```json
{
  "success": true,
  "data": {
    "total_listings": 5,
    "verified_listings": 3,
    "pending_listings": 2,
    "total_views": 156,
    "total_revenue": "850.50",
    "average_price": "650.00"
  }
}
```

---

## 7. OTP Operations

### Request OTP
**POST** `{{base_url}}/otp/request`

**Body:**
```json
{
  "phone_number": "+233501234567"
}
```

**Expected Response (200):**
```json
{
  "success": true,
  "message": "OTP sent to your phone number",
  "debug_otp": 123456,
  "expires_in_minutes": 10
}
```

**Note:** `debug_otp` only shown in APP_DEBUG=true

---

### Verify OTP
**POST** `{{base_url}}/otp/verify`

**Body:**
```json
{
  "phone_number": "+233501234567",
  "otp_code": "123456"
}
```

**Expected Response (200):**
```json
{
  "success": true,
  "message": "OTP verified successfully"
}
```

---

## 8. Error Testing

### Test Unauthorized Access
**GET** `{{base_url}}/user/profile`
(Without Bearer Token)

**Expected Response (401):**
```json
{
  "success": false,
  "message": "Not authenticated."
}
```

---

### Test Validation Errors
**POST** `{{base_url}}/auth/register`

**Body (invalid email):**
```json
{
  "name": "Test",
  "email": "not-an-email",
  "phone_number": "invalid",
  "password": "short",
  "password_confirmation": "different",
  "role": "invalid"
}
```

**Expected Response (422):**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field must be a valid email"],
    "phone_number": ["The phone_number field has an invalid format"],
    "password": ["The password must be at least 8 characters"],
    ...
  }
}
```

---

### Test Permission Denied
Try updating someone else's listing:
**PUT** `{{base_url}}/listings/5` (someone else's listing)

**Expected Response (403):**
```json
{
  "success": false,
  "message": "You can only update your own listings."
}
```

---

## 9. Complete Test Checklist

- [ ] Register as tenant
- [ ] Register as landlord
- [ ] Login with email/password
- [ ] Get current user profile
- [ ] Refresh token
- [ ] Logout (verify 401 on next auth request)
- [ ] Search listings (no auth)
- [ ] View single listing
- [ ] Create listing (as landlord)
- [ ] Update own listing
- [ ] Get my listings
- [ ] Delete own listing
- [ ] Add to favorites
- [ ] Check if favorited
- [ ] Get my favorites
- [ ] Remove favorite
- [ ] Clear all favorites
- [ ] Initiate viewing fee payment
- [ ] Check payment status
- [ ] Initiate deposit payment
- [ ] Get payment history
- [ ] Update user profile
- [ ] Change password
- [ ] Get landlord statistics (as landlord)
- [ ] Request OTP
- [ ] Verify OTP
- [ ] Test 401 Unauthorized
- [ ] Test 422 Validation errors
- [ ] Test 403 Permission denied

---

## 10. Common Issues & Solutions

### Issue: "user not found" on login
**Solution:** Make sure you registered first and email matches exactly

### Issue: Token not persisting
**Solution:** In Postman, use Tests tab to automatically set token:
```javascript
if (pm.response.code === 200 || pm.response.code === 201) {
  pm.environment.set("auth_token", pm.response.json().data.token);
}
```

### Issue: CORS errors in browser
**Solution:** Ensure Laravel is running with proper CORS headers configured in `config/cors.php`

### Issue: OTP not showing
**Solution:** Check `APP_DEBUG=true` in .env file. In production, OTP will be sent via SMS

### Issue: Payment status always "pending"
**Solution:** This is expected. Real MoMo integration needed to complete payments. Webhook endpoint accepts confirmation.

---

## 11. Postman Collection Template

Save this as `collection.json` and import into Postman:

[See API_DOCUMENTATION.md for full endpoint list]

**Import Instructions:**
1. Postman → Import
2. Select file or paste URL
3. Choose environment variables
4. Run collection tests automatically

---

**Created:** January 2024
**Last Updated:** January 2024
