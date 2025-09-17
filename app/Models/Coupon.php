<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Coupon
 * @package App\Models
 *
 * @property int id
 * @property int coin
 * @property string link
 * @property string title
 * @property Media media
 */
class Coupon extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'title',
        'category_id',
        'coin',
        'details',
        'coupon_media_id',
        'link',
        'type',        
        'status',
    ];

    /**
     * @return BelongsTo
     */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'coupon_media_id');
    }

   /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

   /**
     * @return HasMany
     */
    public function couponRedeem(): HasMany
    {
        return $this->hasMany(CouponRedeem::class, 'coupon_id');
    }

    public function school()
    {
    	return $this->belongsTo(School::class);
    }
}
