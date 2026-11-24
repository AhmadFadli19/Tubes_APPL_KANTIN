<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'stall_id',
        'category_id',
        'name',
        'description',
        'price',
        'original_price',
        'photo',
        'stock',
        'is_available',
        'prep_time',
        'spice_levels',
        'portion_options',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'original_price' => 'decimal:2',
            'stock' => 'integer',
            'is_available' => 'boolean',
            'spice_levels' => 'array',
            'portion_options' => 'array',
        ];
    }

    public function stall(): BelongsTo
    {
        return $this->belongsTo(Stall::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getEffectivePriceAttribute(): float
    {
        return (float) $this->price;
    }

    public function getPhotoUrlAttribute(): string
    {
        if (empty($this->photo)) {
            return asset('assets/figma/6a55e.png');
        }

        if (str_starts_with($this->photo, 'http://') || str_starts_with($this->photo, 'https://')) {
            return $this->photo;
        }

        return asset($this->photo);
    }

    public function isAvailable(): bool
    {
        return $this->is_available && $this->stock > 0;
    }
}
