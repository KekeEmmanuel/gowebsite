<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactMessage extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const STATUS_NEW = 'new';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'service_type_id',
        'message',
        'status',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'resolved_at',
        'meta',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'meta' => 'array',
    ];

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function markResolved(): void
    {
        $this->forceFill([
            'status' => self::STATUS_CLOSED,
            'resolved_at' => now(),
        ])->save();
    }
}
