# Housing Marketplace Database Schema & Eloquent Models Guide

## Overview
This guide covers the database schema and Eloquent models for the Affordable Housing & Room Rental Marketplace built with Laravel 11.

---

## Database Schema

### 1. **Users Table** (Modified)

**Purpose:** Store user accounts with authentication and role management

**New Columns Added:**
```
- phone_number (string, unique): Primary contact number
- otp_code (string, nullable): One-time password for authentication
- otp_expires_at (timestamp, nullable): Expiration time for OTP
- role (enum: 'tenant' | 'landlord'): User type, defaults to 'tenant'
- profile_info (json, nullable): Additional profile data
```

**Migration File:** `2026_03_05_000001_add_columns_to_users_table.php`

---

### 2. **Listings Table** (New)

**Purpose:** Store rental property listings created by landlords

**Schema:**
```
- id (primary key)
- landlord_id (FK to users.id): Property owner
- title (string): Property title
- description (text): Detailed property description
- price (decimal 10,2): Monthly rental price
- bedrooms (integer): Number of bedrooms
- bathrooms (integer): Number of bathrooms
- property_type (enum): apartment|house|studio|shared_room|bungalow
- location_lat (decimal 10,8): Latitude for Google Maps
- location_long (decimal 11,8): Longitude for Google Maps
- location_address (string): Full address text
- verification_status (enum): pending|approved|rejected (default: pending)
- rejection_reason (text, nullable): Admin rejection reason
- is_available (boolean, default: true)
- view_count (integer, default: 0): Track popularity
- created_at, updated_at (timestamps)
- deleted_at (soft delete)
```

**Migration File:** `2026_03_05_000002_create_listings_table.php`

---

### 3. **Photos Table** (New)

**Purpose:** Store up to 3 photos per listing with 3-photo limit enforcement

**Schema:**
```
- id (primary key)
- listing_id (FK to listings.id): Associated listing
- photo_path (string): File system path
- photo_url (string): Public URL for display
- order (integer, 1-3): Photo sequence order
- is_primary (boolean, default: false): Marks primary/main photo
- created_at, updated_at (timestamps)

CONSTRAINT:
- max 3 photos per listing (enforced by application logic)
- UNIQUE(listing_id, order)
```

**Migration File:** `2026_03_05_000003_create_photos_table.php`

**Photo Limit Logic:**
- Use `Photo::hasReachedPhotoLimit($listingId)` to check if listing is at max
- Use `Photo::getRemainingPhotoSlots($listingId)` to get available slots

---

### 4. **Favorites Table** (New)

**Purpose:** Allow tenants to save/bookmark property listings

**Schema:**
```
- id (primary key)
- user_id (FK to users.id): Tenant who favorited
- listing_id (FK to listings.id): Favorited property
- created_at, updated_at (timestamps)

CONSTRAINT:
- UNIQUE(user_id, listing_id): Each user can favorite a listing only once
```

**Migration File:** `2026_03_05_000004_create_favorites_table.php`

---

### 5. **Messages Table** (New)

**Purpose:** In-app messaging/chat between tenants and landlords

**Schema:**
```
- id (primary key)
- sender_id (FK to users.id): Message author
- receiver_id (FK to users.id): Message recipient
- listing_id (FK to listings.id, nullable): Related property
- message (text): Message content
- is_read (boolean, default: false)
- read_at (timestamp, nullable)
- created_at, updated_at (timestamps)
- deleted_at (soft delete)
```

**Migration File:** `2026_03_05_000005_create_messages_table.php`

---

### 6. **Payments Table** (New)

**Purpose:** Track MoMo and other payment transactions for deposits and viewing fees

**Schema:**
```
- id (primary key)
- tenant_id (FK to users.id): Payer
- landlord_id (FK to users.id): Payment recipient
- listing_id (FK to listings.id): Property reference
- amount (decimal 12,2): Payment amount
- payment_type (enum): deposit|viewing_fee|rent|other (default: deposit)
- payment_method (enum): momo|card|bank_transfer|cash (default: momo)
- payment_status (enum): pending|completed|failed|refunded (default: pending)
- transaction_id (string, nullable, unique): MoMo transaction reference
- momo_network (string, nullable): MTN|Vodafone|AirtelTigo
- description (text, nullable): Payment notes
- paid_at (timestamp, nullable): When payment completed
- created_at, updated_at (timestamps)
- deleted_at (soft delete)
```

**Migration File:** `2026_03_05_000006_create_payments_table.php`

---

## Eloquent Models

### User Model

**File:** `app/Models/User.php`

**Key Features:**
- Implements `HasApiTokens` for Laravel Sanctum authentication
- Casts: `profile_info` as JSON array, `otp_expires_at` as datetime

**Relationships:**

```php
// Landlord relationships
$user->listings()           // HasMany: Listings posted by landlord
$user->paymentsAsLandlord() // HasMany: Payments received

// Tenant relationships
$user->favorites()          // BelongsToMany: Saved listings
$user->paymentsAsTenant()  // HasMany: Payments made

// Messaging
$user->sentMessages()      // HasMany: Sent messages
$user->receivedMessages()  // HasMany: Received messages
```

**Scopes:**
```php
User::landlords()  // Get all landlords
User::tenants()    // Get all tenants
```

**Example Usage:**
```php
// Get a landlord's listings
$listings = auth()->user()->listings;

// Get tenant's favorite listings
$favorites = auth()->user()->favorites;

// Mark message as read
$message->markAsRead();
```

---

### Listing Model

**File:** `app/Models/Listing.php`

**Key Features:**
- Uses soft deletes
- Casts verification_status and property_type as strings
- Location coordinates stored as floats for Google Maps integration

**Relationships:**

```php
$listing->landlord()       // BelongsTo: Property owner
$listing->photos()         // HasMany: Listing photos (ordered)
$listing->primaryPhoto()   // HasOne: Main photo
$listing->favoritedBy()    // BelongsToMany: Users who favorited
$listing->messages()       // HasMany: Related messages
$listing->payments()       // HasMany: Payments for this listing
```

**Methods:**

```php
$listing->isFavoritedBy($userId)     // Check if user favorited
$listing->getPhotosCountAttribute()  // Get photo count
```

**Scopes:**

```php
Listing::verified()              // Only approved listings
Listing::pending()               // Listings awaiting approval
Listing::available()             // Only available properties
Listing::byPropertyType('house') // Filter by type
Listing::byLandlord($id)         // Listings by specific landlord
Listing::byPriceRange(500, 2000) // Price filter
Listing::byBedrooms(2)           // Filter by bedrooms
```

**Example Usage:**
```php
// Get all approved apartments
$apartments = Listing::verified()
    ->byPropertyType('apartment')
    ->get();

// Get landlord's listings with photos
$listings = Listing::byLandlord($landlordId)
    ->with('photos')
    ->get();

// Check if user favorited listing
if ($listing->isFavoritedBy(auth()->id())) {
    // Show unfavorite button
}
```

---

### Photo Model

**File:** `app/Models/Photo.php`

**Key Features:**
- Enforce 3-photo limit per listing
- Support for primary/main photo designation
- Photo ordering system

**Relationships:**

```php
$photo->listing()  // BelongsTo: Associated listing
```

**Methods:**

```php
// Static methods
Photo::hasReachedPhotoLimit($listingId)      // Boolean: listing at max?
Photo::getRemainingPhotoSlots($listingId)    // Integer: slots available

// Instance methods
$photo->markAsPrimary()  // Set as primary photo for listing
```

**Scopes:**

```php
Photo::primary()  // Get only primary photos
```

**Example Usage:**
```php
// Check if can add more photos
if (!Photo::hasReachedPhotoLimit($listingId)) {
    // Allow photo upload
}

// Get remaining slots
$remaining = Photo::getRemainingPhotoSlots($listingId);
// $remaining = 2 if 1 photo exists

// Mark a photo as primary
$photo = Photo::find($photoId);
$photo->markAsPrimary();
```

---

### Favorite Model

**File:** `app/Models/Favorite.php`

**Key Features:**
- Simple pivot model for user-listing relationship
- Tracks when listing was favorited

**Relationships:**

```php
$favorite->user()    // BelongsTo: User who favorited
$favorite->listing() // BelongsTo: Favorited listing
```

**Scopes:**

```php
Favorite::forUser($userId)        // Favorites by specific user
Favorite::forListing($listingId)  // Users who favorited listing
```

**Example Usage:**
```php
// Get user's favorite listings
$favorites = Favorite::forUser(auth()->id())
    ->with('listing.photos')
    ->get();

// Get all users who favorited a listing
$users = Favorite::forListing($listing->id)
    ->with('user')
    ->get();
```

---

### Message Model

**File:** `app/Models/Message.php`

**Key Features:**
- Track read status with timestamp
- Soft deletes for data preservation
- Conversation grouping

**Relationships:**

```php
$message->sender()   // BelongsTo: Message author
$message->receiver() // BelongsTo: Message recipient
$message->listing()  // BelongsTo: Related property (nullable)
```

**Methods:**

```php
$message->markAsRead()  // Mark as read with timestamp
```

**Scopes:**

```php
Message::unread()                           // Only unread messages
Message::conversation($userId1, $userId2)   // Get conversation
Message::forListing($listingId)            // Messages for listing
Message::receivedBy($userId)               // Messages received by user
Message::sentBy($userId)                   // Messages sent by user
```

**Example Usage:**
```php
// Get unread messages for authenticated user
$unread = Message::receivedBy(auth()->id())
    ->unread()
    ->with('sender')
    ->get();

// Get conversation between two users
$conversation = Message::conversation($user1Id, $user2Id)->get();

// Mark message as read
$message->markAsRead();
```

---

### Payment Model

**File:** `app/Models/Payment.php`

**Key Features:**
- Support for MoMo, card, bank transfer, and cash payments
- Track transaction IDs for MoMo integration
- Soft deletes for audit trail
- Payment status management

**Relationships:**

```php
$payment->tenant()   // BelongsTo: Payer (tenant)
$payment->landlord() // BelongsTo: Recipient (landlord)
$payment->listing()  // BelongsTo: Property reference
```

**Methods:**

```php
$payment->markAsCompleted()  // Set status to completed with timestamp
$payment->markAsFailed()     // Set status to failed
$payment->markAsRefunded()   // Set status to refunded
```

**Scopes:**

```php
Payment::completed()         // Only successful payments
Payment::pending()           // Awaiting completion
Payment::failed()            // Failed transactions
Payment::momoPayments()      // Only MoMo payments
Payment::byType('deposit')   // Filter by payment type
Payment::forTenant($userId)  // Payments by tenant
Payment::forLandlord($userId) // Payments to landlord
```

**Example Usage:**
```php
// Get deposit payments for a listing
$deposits = Payment::where('listing_id', $listingId)
    ->completed()
    ->get();

// Process successful MoMo payment
$payment = Payment::where('transaction_id', $momoTransactionId)->first();
$payment->markAsCompleted();

// Get landlord's total revenue from deposits
$deposits = Payment::forLandlord($landlordId)
    ->completed()
    ->byType('deposit')
    ->get();
$totalRevenue = $deposits->sum('amount');
```

---

## Running Migrations

To set up the database:

```bash
# Run all migrations
php artisan migrate

# Rollback and re-run (development)
php artisan migrate:refresh

# Rollback everything
php artisan migrate:rollback
```

---

## Model Relationships Summary

```
User
├── hasMany: Listing (as landlord_id)
├── belongsToMany: Listing (via favorites)
├── hasMany: Message (as sender_id)
├── hasMany: Message (as receiver_id)
├── hasMany: Payment (as tenant_id)
└── hasMany: Payment (as landlord_id)

Listing
├── belongsTo: User (landlord)
├── hasMany: Photo
├── hasOne: Photo (primary)
├── belongsToMany: User (via favorites)
├── hasMany: Message
└── hasMany: Payment

Photo
└── belongsTo: Listing

Favorite
├── belongsTo: User
└── belongsTo: Listing

Message
├── belongsTo: User (sender)
├── belongsTo: User (receiver)
└── belongsTo: Listing (nullable)

Payment
├── belongsTo: User (tenant)
├── belongsTo: User (landlord)
└── belongsTo: Listing
```

---

## Database Constraints

### Foreign Keys
- All foreign keys implement **CASCADE DELETE**
- Orphaned records automatically cleaned up

### Unique Constraints
- `users.email` (unique)
- `users.phone_number` (unique)
- `photos.listing_id, order` (unique pair)
- `favorites.user_id, listing_id` (unique pair)
- `payments.transaction_id` (unique)

### Check Constraints (Application Level)
- **Photo Limit:** Max 3 photos per listing
  - Validate before insertion in controller
  - Use `Photo::hasReachedPhotoLimit()`

- **Verification Status:** Defaults to 'pending'
  - Only admins can change to 'approved' or 'rejected'

---

## API Authentication

All API endpoints use **Laravel Sanctum** tokens:

```php
// Generate token for user
$token = $user->createToken('api-token')->plainTextToken;

// Protect routes
Route::middleware('auth:sanctum')->group(function () {
    // Protected endpoints
});
```

---

## Next Steps

1. Create **Controllers** for CRUD operations
2. Create **Request Validation** classes
3. Create **API Routes** for endpoints
4. Create **Factories & Seeders** for testing data
5. Implement **Photo Upload** logic with 3-photo limit
6. Implement **MoMo Payment Gateway** integration
7. Create **Admin Panel** for listing verification

---

## File Structure

```
app/Models/
├── User.php          (Updated)
├── Listing.php       (New)
├── Photo.php         (New)
├── Favorite.php      (New)
├── Message.php       (New)
└── Payment.php       (New)

database/migrations/
├── 0001_01_01_000000_create_users_table.php
├── 0001_01_01_000001_create_cache_table.php
├── 0001_01_01_000002_create_jobs_table.php
├── 2026_03_05_000001_add_columns_to_users_table.php       (New)
├── 2026_03_05_000002_create_listings_table.php            (New)
├── 2026_03_05_000003_create_photos_table.php              (New)
├── 2026_03_05_000004_create_favorites_table.php           (New)
├── 2026_03_05_000005_create_messages_table.php            (New)
└── 2026_03_05_000006_create_payments_table.php            (New)
```

---

## Testing Data Examples

```php
// Create a landlord with listings
$landlord = User::factory()->create(['role' => 'landlord']);
$listing = Listing::factory()->create([
    'landlord_id' => $landlord->id,
    'verification_status' => 'approved'
]);

// Create photos
for ($i = 1; $i <= 3; $i++) {
    Photo::create([
        'listing_id' => $listing->id,
        'photo_path' => 'listings/' . $listing->id . '/' . $i . '.jpg',
        'photo_url' => 'https://example.com/listings/' . $listing->id . '/' . $i . '.jpg',
        'order' => $i,
        'is_primary' => $i === 1,
    ]);
}

// Create tenant and favorite listing
$tenant = User::factory()->create(['role' => 'tenant']);
$listing->favoritedBy()->attach($tenant->id);

// Create message
Message::create([
    'sender_id' => $tenant->id,
    'receiver_id' => $landlord->id,
    'listing_id' => $listing->id,
    'message' => 'Is this property available?',
]);

// Create payment
Payment::create([
    'tenant_id' => $tenant->id,
    'landlord_id' => $landlord->id,
    'listing_id' => $listing->id,
    'amount' => 100.00,
    'payment_type' => 'viewing_fee',
    'payment_method' => 'momo',
    'payment_status' => 'pending',
]);
```
