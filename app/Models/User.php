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
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

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

    public function ownsListing(Listing $listing): bool
    {
        return (int) $listing->agent_id === (int) $this->id
            || (int) $listing->owner_id === (int) $this->id;
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
