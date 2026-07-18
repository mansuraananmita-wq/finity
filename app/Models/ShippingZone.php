<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'zone_name',
        'districts',
        'base_cost',
        'per_kg_cost',
        'estimated_days',
        'is_active',
    ];

    protected $casts = [
        'districts' => 'array',
        'base_cost' => 'decimal:2',
        'per_kg_cost' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
