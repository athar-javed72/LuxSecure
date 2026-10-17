<?php

namespace App\Models;

use App\Models\Concerns\LogsAdminActivity;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, HasRoles, LogsAdminActivity, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'country_id',
        'organization_id',
        'preferred_locale',
        'preferred_currency',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'kyc_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'two_factor_confirmed_at' => 'datetime',
    ];

    public function hasTwoFactorEnabled(): bool
    {
        return filled($this->two_factor_confirmed_at);
    }

    public function isPhoneVerified(): bool
    {
        return filled($this->phone_verified_at);
    }

    public function isKycVerified(): bool
    {
        return filled($this->kyc_verified_at);
    }

    public function kycDocuments()
    {
        return $this->hasMany(KycDocument::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function isAdmin(): bool
    {
        return $this->hasAnyRole(['super_admin', 'admin']);
    }

    public function isStaff(): bool
    {
        return $this->can('panel.access');
    }

    public function favorites()
    {
        return $this->belongsToMany(Listing::class, 'favorites')->withTimestamps();
    }

    public function ownedListings()
    {
        return $this->hasMany(Listing::class, 'owner_id');
    }

    public function agentListings()
    {
        return $this->hasMany(Listing::class, 'agent_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function ownsListing(Listing $listing): bool
    {
        if ((int) $listing->agent_id === (int) $this->id
            || (int) $listing->owner_id === (int) $this->id
            || (int) $listing->manager_id === (int) $this->id) {
            return true;
        }

        if ($this->organization_id && $listing->organization_id
            && (int) $this->organization_id === (int) $listing->organization_id
            && $this->hasAnyRole(['hotel_manager', 'organization_admin'])) {
            return true;
        }

        return false;
    }

    public function isCountryScoped(): bool
    {
        return filled($this->country_id) && ! $this->isSuperAdmin();
    }

    public function tenanciesAsTenant()
    {
        return $this->hasMany(Tenancy::class, 'tenant_id');
    }

    public function tenanciesAsLandlord()
    {
        return $this->hasMany(Tenancy::class, 'landlord_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function agentProfile()
    {
        return $this->hasOne(AgentProfile::class);
    }

    public function assignedLeads()
    {
        return $this->hasMany(Lead::class, 'assigned_agent_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public static function superAdminCount(): int
    {
        return static::role('super_admin')->count();
    }

    public function isLastSuperAdmin(): bool
    {
        return $this->isSuperAdmin() && static::superAdminCount() <= 1;
    }
}
