<?php

namespace App\Models;

use App\Models\Concerns\LogsAdminActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KycDocument extends Model
{
    use LogsAdminActivity;

    public const TYPES = ['cnic', 'license', 'registry'];

    public const STATUSES = ['pending', 'approved', 'rejected'];

    protected $fillable = [
        'user_id',
        'type',
        'file_path',
        'status',
        'reviewed_by',
        'reason',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
