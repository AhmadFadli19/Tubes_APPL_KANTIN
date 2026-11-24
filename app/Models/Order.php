<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'user_id',
        'stall_id',
        'queue_number',
        'order_date',
        'status', // 'menunggu', 'diproses', 'siap_diambil', 'selesai', 'batal'
        'payment_status', // 'belum_bayar', 'lunas', 'kedaluarsa'
        'payment_method',
        'payment_time',
        'subtotal',
        'service_fee',
        'discount',
        'total_price',
        'notes',
        'estimated_ready_time',
    ];

    protected function casts(): array
    {
        return [
            'order_date' => 'date',
            'payment_time' => 'datetime',
            'subtotal' => 'decimal:2',
            'service_fee' => 'decimal:2',
            'discount' => 'decimal:2',
            'total_price' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stall(): BelongsTo
    {
        return $this->belongsTo(Stall::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'menunggu' => 'Menunggu Konfirmasi Penjual',
            'diproses' => 'Sedang Diproses & Dimasak',
            'siap_diambil' => 'Siap Diambil di Stan',
            'selesai' => 'Selesai Diambil',
            'batal' => 'Dibatalkan',
            default => $this->status,
        };
    }

    public function getStatusStepAttribute(): int
    {
        return match ($this->status) {
            'menunggu' => 1,
            'diproses' => 2,
            'siap_diambil' => 3,
            'selesai' => 4,
            default => 1,
        };
    }
}
