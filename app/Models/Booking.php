<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'itinerary_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'country',
        'number_of_travellers',
        'preferred_travel_date',
        'special_requests',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'preferred_travel_date' => 'date',
        'number_of_travellers' => 'integer',
    ];

    public function itinerary(): BelongsTo
    {
        return $this->belongsTo(Itinerary::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
