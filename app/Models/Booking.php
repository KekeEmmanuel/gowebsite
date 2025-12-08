<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_package_id',
        'full_name',
        'email',
        'phone',
        'whatsapp',
        'travel_date',
        'number_of_travelers',
        'customization_data',
        'special_requests',
        'status',
        'admin_notes',
        'completed_at',
    ];

    protected $casts = [
        'travel_date' => 'date',
        'number_of_travelers' => 'integer',
        'customization_data' => 'array',
        'completed_at' => 'datetime',
    ];

    public function tourPackage()
    {
        return $this->belongsTo(TourPackage::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }
}
