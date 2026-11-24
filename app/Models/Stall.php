<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stall extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'code',
        'location',
        'description',
        'culinary_category',
        'opening_hours',
        'contact_phone',
        'bank_info',
        'is_open',
        'max_active_queues',
        'sla_minutes',
        'auto_accept_qris',
        'sound_notification',
        'rating',
        'estimated_time',
        'image',
    ];

    protected function casts(): array
    {
        return [
            'is_open' => 'boolean',
            'auto_accept_qris' => 'boolean',
            'sound_notification' => 'boolean',
            'max_active_queues' => 'integer',
            'sla_minutes' => 'integer',
            'rating' => 'decimal:1',
        ];
    }

    public function getImageUrlAttribute(): string
    {
        if (empty($this->image)) {
            return asset('assets/figma/6a55e.png');
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        return asset($this->image);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
