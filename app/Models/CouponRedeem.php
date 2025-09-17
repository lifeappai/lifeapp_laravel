<?php

namespace App\Models;

use App\Http\Resources\MediaResource;
use App\Interfaces\Coinable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CouponRedeem
 * @package App\Models
 *
 * @property int id
 * @property int coins
 *
 * @property User user
 * @property Coupon coupon
 */
class CouponRedeem extends Model implements Coinable
{
    use HasFactory;

    protected $table = 'coupon_redeems';

    protected $fillable = [
        'user_id',
        'coupon_id',
        'coins',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

    /**
     * @return string
     */
    public function getCoinableType()
    {
        return "Coupon";
    }

    /**
     * @return array
     */
    public function getCoinableObject()
    {
        return [
            "id" => $this->coupon->id,
            "title" => $this->coupon->title,
            "coupon_media" => new MediaResource($this->coupon->media),
        ];
    }
}
