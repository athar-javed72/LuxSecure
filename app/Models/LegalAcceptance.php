<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegalAcceptance extends Model
{
    protected $fillable = [
        'user_id',
        'page_id',
        'version',
        'ip',
        'accepted_at',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public static function record(User $user, Page $page, ?string $ip = null): self
    {
        return static::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'page_id' => $page->id,
                'version' => $page->version,
            ],
            [
                'ip' => $ip,
                'accepted_at' => now(),
            ]
        );
    }

    public static function hasAccepted(User $user, Page $page): bool
    {
        return static::query()
            ->where('user_id', $user->id)
            ->where('page_id', $page->id)
            ->where('version', $page->version)
            ->exists();
    }
}
