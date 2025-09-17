<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LaSubjectCouponCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'la_subject_id',
        'user_id',
        'coupon_code',
        'unlock_coupon_at',
    ];

    /**
     * @return HasMany
     */
    public function laSubject(): BelongsTo
    {
        return $this->belongsTo(LaSubject::class, 'la_subject_id');
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
