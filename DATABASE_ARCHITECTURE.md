# Database Architecture & ERD

## Entity Relationship Diagram

```
┌─────────────────────────────────────────────────────────────────────┐
│                          DATABASE SCHEMA                            │
└─────────────────────────────────────────────────────────────────────┘

                                  USERS
                        ┌──────────────────────┐
                        │ id (PK)              │
                        │ name                 │
                        │ email (UNIQUE)       │
                        │ phone_number (UNIQUE)│
                        │ password             │
                        │ otp_code             │
                        │ otp_expires_at       │
                        │ role (enum)          │
                        │ profile_info (JSON)  │
                        │ created_at           │
                        │ updated_at           │
                        └──────────────────────┘
                                ▲ ▲ ▲ ▲
                          ╱─────┘ │ │ └─────╲
                         ╱        │ │        ╲
                        ╱         │ │         ╲
            has_many: listings   │ │   has_many: paymentsAsLandlord
            (landlord_id)        │ │   (landlord_id)
                        ╱         │ │         ╲
                         ╲        │ │        ╱
                          ╲─────┐ │ │ ┌─────╱
                        ▼ ▼ ▼ ▼ 
                     LISTINGS
            ┌─────────────────────────────┐
            │ id (PK)                     │
            │ landlord_id (FK)            │
            │ title                       │
            │ description                 │
            │ price                       │
            │ bedrooms                    │
            │ bathrooms                   │
            │ property_type (enum)        │
            │ location_lat                │
            │ location_long               │
            │ location_address            │
            │ verification_status (enum)  │
            │ rejection_reason            │
            │ is_available                │
            │ view_count                  │
            │ created_at, updated_at      │
            │ deleted_at (soft delete)    │
            └─────────────────────────────┘
                        │ ▲ ▲ ▲ ▲ ▲
                        │ │ │ │ │ │
          ┌─────────────┼─┘ │ │ │ └──────────────┐
          │             │   │ │ │                │
          │ has_many:   │   │ │ │ has_many:     │
          │ photos      │   │ │ │ messages      │
          │             │   │ │ │                │
          │             │   │ │ └────has_many: paymentsAsTenant
          │             │   │       (when tenant.id = tenant_id)
          │             │   │
          │             │   └────belongs_to_many: favoritedBy (via FAVORITES)
          │             │
          ▼             ▼
        PHOTOS      FAVORITES
    ┌─────────────┐ ┌──────────────────┐
    │ id (PK)     │ │ id (PK)          │
    │ listing_id  │ │ user_id (FK)     │
    │ photo_path  │ │ listing_id (FK)  │
    │ photo_url   │ │ created_at       │
    │ order       │ │ updated_at       │
    │ is_primary  │ │ UNIQUE(user_id,  │
    │ created_at  │ │        listing_id)
    │ updated_at  │ └──────────────────┘
    │ UNIQUE(     │         │ ▲
    │ listing_id, │         │ │ belongs_to: user
    │ order)      │         │ │
    └─────────────┘         │ │
                            │ │ belongs_to: listing
                            │ │
                            ▼ ▼
                          USERS


             MESSAGES
    ┌──────────────────────────┐
    │ id (PK)                  │
    │ sender_id (FK → users)   │
    │ receiver_id (FK → users) │
    │ listing_id (FK → listings)
    │ message (TEXT)           │
    │ is_read                  │
    │ read_at                  │
    │ created_at, updated_at   │
    │ deleted_at               │
    └──────────────────────────┘
            ▲  ▲  ▲
            │  │  └─ belongs_to: listing
            │  │
            │  └─ belongs_to: receiver (User)
            │
            └─ belongs_to: sender (User)


             PAYMENTS
    ┌──────────────────────────────┐
    │ id (PK)                      │
    │ tenant_id (FK → users)       │
    │ landlord_id (FK → users)     │
    │ listing_id (FK → listings)   │
    │ amount (DECIMAL)             │
    │ payment_type (enum)          │
    │ payment_method (enum)        │
    │ payment_status (enum)        │
    │ transaction_id (UNIQUE)      │
    │ momo_network                 │
    │ description                  │
    │ paid_at                      │
    │ created_at, updated_at       │
    │ deleted_at                   │
    └──────────────────────────────┘
            ▲  ▲  ▲
            │  │  └─ belongs_to: listing
            │  │
            │  └─ belongs_to: landlord (User)
            │
            └─ belongs_to: tenant (User)
```

---

## Table Relationships Matrix

| Table | Relationship | Target Table | Type | Foreign Key |
|-------|-------------|--------------|------|-------------|
| **listings** | landlord | users | BelongsTo | landlord_id |
| **photos** | listing | listings | BelongsTo | listing_id |
| **favorites** | user | users | BelongsTo | user_id |
| **favorites** | listing | listings | BelongsTo | listing_id |
| **messages** | sender | users | BelongsTo | sender_id |
| **messages** | receiver | users | BelongsTo | receiver_id |
| **messages** | listing | listings | BelongsTo | listing_id (nullable) |
| **payments** | tenant | users | BelongsTo | tenant_id |
| **payments** | landlord | users | BelongsTo | landlord_id |
| **payments** | listing | listings | BelongsTo | listing_id |
| **users** | listings | listings | HasMany | landlord_id |
| **users** | favorites | listings | BelongsToMany | - |
| **listings** | photos | photos | HasMany | listing_id |
| **listings** | favoritedBy | users | BelongsToMany | - |
| **listings** | messages | messages | HasMany | listing_id |
| **listings** | payments | payments | HasMany | listing_id |

---

## Data Flow & Relationships

### User Roles & Permissions

```
USER (role: 'landlord')
├── Create listings ✓
├── Upload photos (max 3 per listing)
├── Receive messages
├── Receive payments
└── View payment history

USER (role: 'tenant')
├── Create favorites
├── Send messages
├── Make payments
└── View payment history
```

### Listing Lifecycle

```
1. CREATION (by landlord)
   Listing created with verification_status = 'pending'
   
2. OPTIMIZATION (by landlord)
   - Add description
   - Upload 1-3 photos
   - Set location coordinates
   - Review details
   
3. SUBMISSION
   Listing is ready for approval
   
4. REVIEW (by admin)
   - Check all details
   - Verify authenticity
   
5. APPROVAL/REJECTION
   - Approved → visible to tenants
   - Rejected → landlord updates and resubmits
   
6. ACTIVE
   - Tenants can view, favorite, message
   - Payments can be made
   
7. ARCHIVED (soft delete)
   - Landlord marks unavailable
   - Can be restored if needed
```

### Payment Flow

```
TENANT initiates payment
    ↓
Payment record created (status: pending)
    ↓
Integrated with MoMo/Payment Gateway
    ↓
Payment confirmation received
    ↓
Payment status updated to 'completed'
    ↓
Receipt generated
    ↓
Landlord receives notification
    ↓
Funds transferred to landlord
```

---

## Indexes for Performance

All tables include strategic indexes:

```sql
-- Users Table
INDEX: email, phone_number (UNIQUE - faster lookups)

-- Listings Table
INDEX: landlord_id + verification_status (common filter)
INDEX: is_available (quick availability checks)

-- Photos Table
INDEX: listing_id (get listing photos)
UNIQUE: listing_id + order (prevent duplicates)

-- Favorites Table
UNIQUE: user_id + listing_id (prevent duplicate favorites)
INDEX: user_id + created_at (sort user's favorites)
INDEX: listing_id (see who favorited)

-- Messages Table
INDEX: receiver_id + is_read (unread notifications)
INDEX: sender_id + receiver_id (conversation queries)
INDEX: listing_id + created_at (listing-related messages)

-- Payments Table
INDEX: tenant_id + payment_status (tenant's payment history)
INDEX: landlord_id + created_at (landlord's payments)
INDEX: listing_id + payment_status (listing's payments)
UNIQUE: transaction_id (prevent duplicate processing)
```

---

## Schema Constraints & Validations

### Foreign Key Constraints
- **Cascade Delete:** Deleting a user deletes their listings, messages, and payments
- **Referential Integrity:** Cannot create listing without valid landlord_id

### Unique Constraints
```sql
users.email (UNIQUE)
users.phone_number (UNIQUE)
photos (UNIQUE listing_id + order)
favorites (UNIQUE user_id + listing_id)
payments.transaction_id (UNIQUE)
```

### Enum Values

**Users.role:**
- `tenant` - Regular user looking for rental
- `landlord` - Property owner/manager

**Listings.property_type:**
- `apartment` - Flat/apartment
- `house` - Single/detached house
- `studio` - Studio apartment
- `shared_room` - Room in shared property
- `bungalow` - Bungalow

**Listings.verification_status:**
- `pending` - Awaiting admin review (default)
- `approved` - Verified and visible to users
- `rejected` - Did not meet requirements

**Payments.payment_type:**
- `deposit` - Security deposit
- `viewing_fee` - Fee to view property
- `rent` - Monthly rent
- `other` - Other payments

**Payments.payment_method:**
- `momo` - Mobile Money (MoMo)
- `card` - Credit/Debit card
- `bank_transfer` - Bank transfer
- `cash` - Cash payment

**Payments.payment_status:**
- `pending` - Awaiting payment
- `completed` - Successfully paid
- `failed` - Payment failed
- `refunded` - Refund processed

---

## Migration Execution Order

The migrations should run in this order for referential integrity:

```
1. 0001_01_01_000000_create_users_table.php
   └─ Creates users table (base for all FK references)

2. 0001_01_01_000001_create_cache_table.php
3. 0001_01_01_000002_create_jobs_table.php

4. 2026_03_05_000001_add_columns_to_users_table.php
   └─ Updates users with new columns

5. 2026_03_05_000002_create_listings_table.php
   └─ References users.id as landlord_id

6. 2026_03_05_000003_create_photos_table.php
   └─ References listings.id

7. 2026_03_05_000004_create_favorites_table.php
   └─ References users.id and listings.id

8. 2026_03_05_000005_create_messages_table.php
   └─ References users.id (twice) and listings.id

9. 2026_03_05_000006_create_payments_table.php
   └─ References users.id (twice) and listings.id
```

---

## Soft Deletes Strategy

**Tables using soft deletes:** listings, messages, payments

This allows:
- Recovery of deleted data
- Historical record keeping
- Audit trails
- Referential integrity maintenance

```php
// Hide soft-deleted records (default)
$listings = Listing::all();

// Include soft-deleted records
$listings = Listing::withTrashed()->get();

// Get only soft-deleted records
$deletedListings = Listing::onlyTrashed()->get();

// Restore deleted record
$listing->restore();

// Permanently delete
$listing->forceDelete();
```

---

## Database Best Practices Implemented

✅ **Normalization:** Data properly normalized (3NF)
✅ **Foreign Keys:** All relationships use constraints
✅ **Indexing:** Indexes on frequently queried columns
✅ **Uniqueness:** Unique constraints where appropriate
✅ **Soft Deletes:** Non-destructive deletion for audit trails
✅ **Timestamps:** Created_at and updated_at for tracking
✅ **JSON Storage:** Profile_info uses JSON for flexibility
✅ **Type Safety:** Enums prevent invalid states
✅ **Scalability:** Proper indexing for large datasets
✅ **Data Integrity:** Cascade delete policies defined

---

## Running Migrations

### Initial Setup

```bash
# Install dependencies
composer install

# Run migrations
php artisan migrate

# Seed database (optional)
php artisan db:seed
```

### Reset Database (Development)

```bash
# Drop and recreate all tables
php artisan migrate:fresh

# Drop, recreate, and seed
php artisan migrate:fresh --seed
```

### Rollback

```bash
# Rollback last batch
php artisan migrate:rollback

# Rollback all migrations
php artisan migrate:reset

# Rollback and re-run
php artisan migrate:refresh
```

### Check Status

```bash
# List migrations
php artisan migrate:status

# Show pending migrations
php artisan migrate --pretend
```

---

## Next Steps for Complete Implementation

### 1. Create Factories for Testing

```bash
php artisan make:factory ListingFactory --model=Listing
php artisan make:factory PhotoFactory --model=Photo
php artisan make:factory MessageFactory --model=Message
php artisan make:factory PaymentFactory --model=Payment
```

### 2. Create Seeders

```bash
php artisan make:seeder ListingSeeder
php artisan make:seeder UserSeeder
php artisan make:seeder PaymentSeeder
```

### 3. Create Controllers

```bash
php artisan make:controller Api/ListingController --api
php artisan make:controller Api/PhotoController --api
php artisan make:controller Api/FavoriteController --api
php artisan make:controller Api/MessageController --api
php artisan make:controller Api/PaymentController --api
```

### 4. Create API Resources

```bash
php artisan make:resource ListingResource
php artisan make:resource PhotoResource
php artisan make:resource MessageResource
php artisan make:resource PaymentResource
```

### 5. Create Form Requests

```bash
php artisan make:request StoreListingRequest
php artisan make:request UpdateListingRequest
php artisan make:request StorePhotoRequest
php artisan make:request StoreMessageRequest
```

### 6. Create Events & Listeners

```bash
php artisan make:event ListingApproved
php artisan make:event PaymentCompleted
php artisan make:listener SendListingApprovedNotification --event=ListingApproved
```

---

## Testing the Schema

```php
// In tests/Feature/DatabaseSchemaTest.php
use Illuminate\Foundation\Testing\RefreshDatabase;

class DatabaseSchemaTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_users_table_exists()
    {
        $this->assertTrue(Schema::hasTable('users'));
    }
    
    public function test_listings_table_has_required_columns()
    {
        $this->assertTrue(Schema::hasColumn('listings', 'landlord_id'));
        $this->assertTrue(Schema::hasColumn('listings', 'verification_status'));
    }
    
    public function test_photo_limit_works()
    {
        $listing = Listing::factory()->create();
        
        for ($i = 0; $i < 3; $i++) {
            Photo::factory()->create(['listing_id' => $listing->id]);
        }
        
        $this->assertTrue(Photo::hasReachedPhotoLimit($listing->id));
        $this->assertEquals(0, Photo::getRemainingPhotoSlots($listing->id));
    }
}
```

---

## Database Size Estimation (1 Year Growth)

| Table | Records | Approx. Size |
|-------|---------|-------------|
| users | 10,000 | 2 MB |
| listings | 5,000 | 3 MB |
| photos | 12,000 | 2 MB |
| favorites | 50,000 | 2 MB |
| messages | 500,000 | 50 MB |
| payments | 30,000 | 3 MB |
| **TOTAL** | **607,000** | **~62 MB** |

*Estimates based on average Accra market growth*

---

## Security Considerations

1. **OTP Expiration:** Set reasonable expiry times (10-15 minutes)
2. **Transaction IDs:** Prevent duplicate processing with UNIQUE constraint
3. **Soft Deletes:** Keep historical data for audits
4. **SSL/TLS:** Always use HTTPS for sensitive operations
5. **API Rate Limiting:** Prevent abuse on payment endpoints
6. **Input Validation:** Validate all user inputs
7. **Authorization:** Check user roles before operations
8. **Encryption:** Consider encrypting sensitive profile data

---
