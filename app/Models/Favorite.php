<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favorite extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'listing_id',
    ];

    /**
     * Disable timestamps if not needed or keep them as-is.
     * Timestamps are useful for sorting favorites by date added.
     */
    public $timestamps = true;

    /**
     * Get the user who favorited the listing.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the favorited listing.
     */
    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    /**
     * Scope to get favorites for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get favorites for a specific listing.
     */
    public function scopeForListing($query, $listingId)
    {
        return $query->where('listing_id', $listingId);
    }
}
