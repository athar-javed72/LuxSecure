<?php

namespace App\Models;

use App\Models\Concerns\LogsAdminActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class AgentProfile extends Model
{
    use LogsAdminActivity;

    protected $fillable = [
        'user_id',
        'phone',
        'whatsapp',
        'license_number',
        'agency',
        'bio',
        'photo_path',
        'is_verified',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo_path) {
            if (str_starts_with($this->photo_path, 'http')) {
                return $this->photo_path;
            }

            return Storage::disk('public')->url($this->photo_path);
        }

        return 'https://ui-avatars.com/api/?name='.urlencode($this->user?->name ?? 'Agent').'&background=0B1C2C&color=C9A227';
    }

    public function getDisplayRoleAttribute(): string
    {
        $parts = array_filter([$this->agency, $this->is_verified ? 'Verified' : null]);

        return $parts !== [] ? implode(' · ', $parts) : 'LuxSecure Agent';
    }
}
