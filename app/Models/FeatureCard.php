<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeatureCard extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'icon',
        'title',
        'headline',
        'copy',
        'count_value',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'count_value' => 'integer',
        'display_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('display_order');
    }
}
