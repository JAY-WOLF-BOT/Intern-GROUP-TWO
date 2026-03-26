<?php

namespace App\Constants;

/**
 * Application Constants
 *
 * Centralized definitions for all application constants.
 * Prevents magic strings throughout the codebase.
 */
class AppConstants
{
    // ========================================================================
    // USER ROLES
    // ========================================================================
    const ROLE_TENANT = 'tenant';
    const ROLE_LANDLORD = 'landlord';
    const ROLE_ADMIN = 'admin';

    const VALID_ROLES = [
        self::ROLE_TENANT,
        self::ROLE_LANDLORD,
        self::ROLE_ADMIN,
    ];

    // ========================================================================
    // LISTING STATUS
    // ========================================================================
    const LISTING_STATUS_PENDING = 'pending';
    const LISTING_STATUS_APPROVED = 'approved';
    const LISTING_STATUS_REJECTED = 'rejected';
    const LISTING_STATUS_ARCHIVED = 'archived';

    const VALID_LISTING_STATUSES = [
        self::LISTING_STATUS_PENDING,
        self::LISTING_STATUS_APPROVED,
        self::LISTING_STATUS_REJECTED,
        self::LISTING_STATUS_ARCHIVED,
    ];

    // ========================================================================
    // LISTING AVAILABILITY
    // ========================================================================
    const LISTING_AVAILABLE = true;
    const LISTING_UNAVAILABLE = false;

    // ========================================================================
    // PROPERTY TYPES
    // ========================================================================
    const PROPERTY_TYPE_APARTMENT = 'Apartment';
    const PROPERTY_TYPE_HOUSE = 'House';
    const PROPERTY_TYPE_STUDIO = 'Studio';
    const PROPERTY_TYPE_CONDO = 'Condo';
    const PROPERTY_TYPE_TOWNHOUSE = 'Townhouse';
    const PROPERTY_TYPE_BUNGALOW = 'Bungalow';

    const VALID_PROPERTY_TYPES = [
        self::PROPERTY_TYPE_APARTMENT,
        self::PROPERTY_TYPE_HOUSE,
        self::PROPERTY_TYPE_STUDIO,
        self::PROPERTY_TYPE_CONDO,
        self::PROPERTY_TYPE_TOWNHOUSE,
        self::PROPERTY_TYPE_BUNGALOW,
    ];

    // ========================================================================
    // PAYMENT STATUS
    // ========================================================================
    const PAYMENT_STATUS_PENDING = 'pending';
    const PAYMENT_STATUS_PROCESSING = 'processing';
    const PAYMENT_STATUS_COMPLETED = 'completed';
    const PAYMENT_STATUS_FAILED = 'failed';
    const PAYMENT_STATUS_CANCELLED = 'cancelled';
    const PAYMENT_STATUS_REFUNDED = 'refunded';

    const VALID_PAYMENT_STATUSES = [
        self::PAYMENT_STATUS_PENDING,
        self::PAYMENT_STATUS_PROCESSING,
        self::PAYMENT_STATUS_COMPLETED,
        self::PAYMENT_STATUS_FAILED,
        self::PAYMENT_STATUS_CANCELLED,
        self::PAYMENT_STATUS_REFUNDED,
    ];

    // ========================================================================
    // PAYMENT TYPE
    // ========================================================================
    const PAYMENT_TYPE_VIEWING_FEE = 'viewing_fee';
    const PAYMENT_TYPE_DEPOSIT = 'deposit_holding';
    const PAYMENT_TYPE_RENT = 'rent_payment';

    const VALID_PAYMENT_TYPES = [
        self::PAYMENT_TYPE_VIEWING_FEE,
        self::PAYMENT_TYPE_DEPOSIT,
        self::PAYMENT_TYPE_RENT,
    ];

    // ========================================================================
    // PAYMENT METHOD
    // ========================================================================
    const PAYMENT_METHOD_MOMO = 'momo';
    const PAYMENT_METHOD_CARD = 'card';
    const PAYMENT_METHOD_BANK = 'bank_transfer';
    const PAYMENT_METHOD_WALLET = 'wallet';

    const VALID_PAYMENT_METHODS = [
        self::PAYMENT_METHOD_MOMO,
        self::PAYMENT_METHOD_CARD,
        self::PAYMENT_METHOD_BANK,
        self::PAYMENT_METHOD_WALLET,
    ];

    // ========================================================================
    // MOBILE NETWORKS
    // ========================================================================
    const NETWORK_MTN = 'MTN';
    const NETWORK_VODAFONE = 'Vodafone';
    const NETWORK_AIRTEL = 'AirtelTigo';

    const VALID_NETWORKS = [
        self::NETWORK_MTN,
        self::NETWORK_VODAFONE,
        self::NETWORK_AIRTEL,
    ];

    // ========================================================================
    // PAGINATION
    // ========================================================================
    const DEFAULT_PAGE_SIZE = 15;
    const MAX_PAGE_SIZE = 100;
    const MIN_PAGE_SIZE = 1;

    // ========================================================================
    // MESSAGE STATUS
    // ========================================================================
    const MESSAGE_READ = true;
    const MESSAGE_UNREAD = false;

    // ========================================================================
    // PHOTO CONSTRAINTS
    // ========================================================================
    const MAX_PHOTOS_PER_LISTING = 3;
    const MAX_PHOTO_SIZE_MB = 5;
    const ALLOWED_PHOTO_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    // ========================================================================
    // API RATE LIMITS (requests per minute)
    // ========================================================================
    const RATE_LIMIT_PUBLIC = 30;
    const RATE_LIMIT_AUTHENTICATED = 60;
    const RATE_LIMIT_LOGIN_ATTEMPT = 5;
    const RATE_LIMIT_OTP = 3;

    // ========================================================================
    // VALIDATION PATTERNS
    // ========================================================================
    const PHONE_PATTERN_GH = '/^(\+233|0)([0-9]{9,10})$/'; // Ghana phone number
    const EMAIL_PATTERN = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';

    // ========================================================================
    // CURRENCY
    // ========================================================================
    const CURRENCY_GHS = 'GHS'; // Ghana Cedis

    // ========================================================================
    // NEIGHBORHOODS
    // ========================================================================
    const VALID_NEIGHBORHOODS = [
        'Accra',
        'Kumasi',
        'Tema',
        'Cape Coast',
        'Sekondi-Takoradi',
        'Tamale',
        'Warri',
        'Lagos',
        'Osu',
        'East Legon',
        'Airport Residential',
        'Dansoman',
        'Achimota',
        'Adabraka',
        'Cantonments',
    ];

    // ========================================================================
    // OTP
    // ========================================================================
    const OTP_EXPIRY_MINUTES = 10;
    const OTP_MAX_ATTEMPTS = 5;

    // ========================================================================
    // CACHE KEYS
    // ========================================================================
    const CACHE_KEY_LISTINGS = 'listings:';
    const CACHE_KEY_USER_FAVORITES = 'user_favorites:';
    const CACHE_KEY_OTP = 'otp:';

    // ========================================================================
    // HELPER METHODS
    // ========================================================================

    /**
     * Get human-readable name for listing status.
     */
    public static function getListingStatusLabel($status): string
    {
        return match ($status) {
            self::LISTING_STATUS_PENDING => 'Pending Approval',
            self::LISTING_STATUS_APPROVED => 'Approved',
            self::LISTING_STATUS_REJECTED => 'Rejected',
            self::LISTING_STATUS_ARCHIVED => 'Archived',
            default => 'Unknown',
        };
    }

    /**
     * Get human-readable name for payment status.
     */
    public static function getPaymentStatusLabel($status): string
    {
        return match ($status) {
            self::PAYMENT_STATUS_PENDING => 'Pending',
            self::PAYMENT_STATUS_PROCESSING => 'Processing',
            self::PAYMENT_STATUS_COMPLETED => 'Completed',
            self::PAYMENT_STATUS_FAILED => 'Failed',
            self::PAYMENT_STATUS_CANCELLED => 'Cancelled',
            self::PAYMENT_STATUS_REFUNDED => 'Refunded',
            default => 'Unknown',
        };
    }

    /**
     * Get human-readable name for payment method.
     */
    public static function getPaymentMethodLabel($method): string
    {
        return match ($method) {
            self::PAYMENT_METHOD_MOMO => 'Mobile Money',
            self::PAYMENT_METHOD_CARD => 'Credit/Debit Card',
            self::PAYMENT_METHOD_BANK => 'Bank Transfer',
            self::PAYMENT_METHOD_WALLET => 'Wallet',
            default => 'Unknown',
        };
    }
}
