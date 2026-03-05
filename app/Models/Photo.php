<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Photo extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'listing_id',
        'photo_path',
        'photo_url',
        'order',
        'is_primary',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'order' => 'integer',
        ];
    }

    /**
     * Get the listing that owns this photo.
     */
    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    /**
     * Scope to get only primary photos.
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Mark this photo as primary for the listing.
     */
    public function markAsPrimary(): void
    {
        // Remove primary status from other photos
        Photo::where('listing_id', $this->listing_id)
            ->update(['is_primary' => false]);

        // Mark this as primary
        $this->update(['is_primary' => true]);
    }

    /**
     * Check if listing has reached 3-photo limit.
     */
    public static function hasReachedPhotoLimit($listingId): bool
    {
        return Photo::where('listing_id', $listingId)->count() >= 3;
    }

    /**
     * Get remaining photos allowed for a listing.
     */
    public static function getRemainingPhotoSlots($listingId): int
    {
        $currentCount = Photo::where('listing_id', $listingId)->count();
        return max(0, 3 - $currentCount);
    }
}
