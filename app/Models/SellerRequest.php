<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'applicant_name',
        'email',
        'phone',
        'stall_name',
        'stall_code',
        'stall_location',
        'stall_description',
        'opening_hours',
        'contact_phone',
        'bank_info',
        'status',
        'admin_notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
