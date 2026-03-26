<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Listing extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'landlord_id',
        'title',
        'description',
        'price',
        'deposit',
        'bedrooms',
        'bathrooms',
        'area_sqft',
        'property_type',
        'location_lat',
        'location_long',
        'location_address',
        'neighborhood',
        'furnished',
        'wifi',
        'parking',
        'security',
        'pool',
        'gym',
        'verification_status',
        'rejection_reason',
        'is_available',
        'view_count',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'verification_status' => 'string',
            'property_type' => 'string',
            'location_lat' => 'float',
            'location_long' => 'float',
            'price' => 'float',
            'deposit' => 'float',
            'area_sqft' => 'integer',
            'is_available' => 'boolean',
            'furnished' => 'boolean',
            'wifi' => 'boolean',
            'parking' => 'boolean',
            'security' => 'boolean',
            'pool' => 'boolean',
            'gym' => 'boolean',
            'view_count' => 'integer',
        ];
    }

    /**
     * Get the landlord who created this listing.
     */
    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    /**
     * Get all photos for this listing.
     */
    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class)->orderBy('order');
    }

    /**
     * Get the primary photo of the listing.
     */
    public function primaryPhoto()
    {
        return $this->hasOne(Photo::class)->where('is_primary', true)->latest('created_at');
    }

    /**
     * Get all users who favorited this listing.
     */
    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    /**
     * Get all messages related to this listing.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->latest();
    }

    /**
     * Get all payments for this listing.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Check if a specific user has favorited this listing.
     */
    public function isFavoritedBy($userId): bool
    {
        return $this->favoritedBy()->where('user_id', $userId)->exists();
    }

    /**
     * Get count of photos for this listing.
     */
    public function getPhotosCountAttribute(): int
    {
        return $this->photos()->count();
    }

    /**
     * Scope to filter by verification status.
     */
    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'approved');
    }

    /**
     * Scope to filter pending listings.
     */
    public function scopePending($query)
    {
        return $query->where('verification_status', 'pending');
    }

    /**
     * Scope to filter available listings.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope to filter by property type.
     */
    public function scopeByPropertyType($query, $type)
    {
        return $query->where('property_type', $type);
    }

    /**
     * Scope to filter by landlord.
     */
    public function scopeByLandlord($query, $landlordId)
    {
        return $query->where('landlord_id', $landlordId);
    }

    /**
     * Scope to filter by price range.
     */
    public function scopeByPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('price', [$minPrice, $maxPrice]);
    }

    /**
     * Scope to filter by bedrooms.
     */
    public function scopeByBedrooms($query, $bedrooms)
    {
        return $query->where('bedrooms', $bedrooms);
    }

    /**
     * Scope to filter by neighborhood.
     */
    public function scopeByNeighborhood($query, $neighborhood)
    {
        return $query->where('neighborhood', 'like', '%' . $neighborhood . '%');
    }

    /**
     * Generate WhatsApp deep link for contacting landlord.
     */
    public function getWhatsAppLinkAttribute(): string
    {
        $phone = $this->landlord->phone_number;
        $message = urlencode("Hi! I'm interested in your property: {$this->title}. Can we discuss the details?");
        return "https://wa.me/{$phone}?text={$message}";
    }
}
