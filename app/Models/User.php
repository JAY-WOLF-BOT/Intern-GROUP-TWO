<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'password',
        'otp_code',
        'otp_expires_at',
        'role',
        'profile_info',
        'profile_picture',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'password' => 'hashed',
            'profile_info' => 'array',
        ];
    }

    /**
     * Get all listings posted by the landlord.
     */
    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class, 'landlord_id');
    }

    /**
     * Get all favorite listings by the tenant.
     */
    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(Listing::class, 'favorites');
    }

    /**
     * Get all messages sent by the user.
     */
    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get all messages received by the user.
     */
    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Get all payments made by the tenant.
     */
    public function paymentsAsTenant(): HasMany
    {
        return $this->hasMany(Payment::class, 'tenant_id');
    }

    /**
     * Get all payments received by the landlord.
     */
    public function paymentsAsLandlord(): HasMany
    {
        return $this->hasMany(Payment::class, 'landlord_id');
    }

    /**
     * Scope to get only landlords.
     */
    public function scopeLandlords($query)
    {
        return $query->where('role', 'landlord');
    }

    /**
     * Scope to get only tenants.
     */
    public function scopeTenants($query)
    {
        return $query->where('role', 'tenant');
    }
}
