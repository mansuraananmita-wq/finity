<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name_en',
        'name_bn',
        'scientific_name',
        'slug',
        'price',
        'stock_qty',
        'description_en',
        'description_bn',
        'care_level',
        'tank_size_liters',
        'weight_grams',
        'image_path',
        'is_featured',
        'avg_rating',
        'review_count',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'avg_rating' => 'decimal:1',
        'is_featured' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(Review::class)
            ->where('is_approved', 1)
            ->latest();
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function wishlistedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'wishlists')->withTimestamps();
    }

    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'bn' ? $this->name_bn : $this->name_en;
    }

    public function getDescriptionAttribute(): string
    {
        return app()->getLocale() === 'bn' ? $this->description_bn : $this->description_en;
    }

    public static function updateRatingStats(?Product $product): void
    {
        if (! $product) {
            return;
        }

        $stats = $product->reviews()
            ->where('is_approved', 1)
            ->selectRaw('COALESCE(AVG(rating), 0) as avg_rating, COUNT(*) as review_count')
            ->first();

        $product->forceFill([
            'avg_rating' => round((float) ($stats->avg_rating ?? 0), 1),
            'review_count' => (int) ($stats->review_count ?? 0),
        ])->save();
    }
}
