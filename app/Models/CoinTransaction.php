<?php

namespace App\Models;

use App\Interfaces\Coinable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class CoinTransaction
 * @package App\Models
 *
 * @property integer id
 * @property float amount
 * @property int type
 * @property integer coinable_id
 * @property integer coinable_type
 *
 * @property User $user
 * @property Coinable coinable
 *
 */
class CoinTransaction extends Model
{
    use HasFactory;

    const TYPE_HEART = 0;
    const TYPE_BRAIN = 1;
    const TYPE_QUIZ = 2;
    const TYPE_COUPON = 3;
    const TYPE_MISSION = 4;
    const TYPE_ADMIN = 5;
    const TYPE_QUERY = 6;
    const TYPE_VISION = 7;
    const TYPE_ASSIGN_TASK = 8;
    const TYPE_CORRECT_SUBMISSION = 9;

    protected $table = "coin_transactions";

    protected $fillable = [
        "user_id",
        "type",
        "amount",
        "coinable_id",
        "coinable_type",
    ];

    protected $appends = [
        'coinable_model',
        'coinable_object',
    ];

    protected $hidden = [
        'coinable_id',
        'coinable_type',
        'coinable',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return MorphTo
     */
    public function coinable()
    {
        return $this->morphTo('coinable');
    }

    /**
     * @param $coinable
     * @return CoinTransaction
     */
    public function setObject($coinable)
    {
        $this->coinable_id = $coinable->id;
        $this->coinable_type = get_class($coinable);
        return $this;
    }

    /**
     * @param $coinable
     * @return CoinTransaction
     */
    public function attachObject($coinable)
    {
        $this->setObject($coinable)
            ->save();
        return $this;
    }

    public function scopeOfObject($query, $object)
    {
        return $query->where('coinable_id', $object->id)
            ->where('coinable_type', get_class($object));
    }

    public function getCoinableModelAttribute()
    {
        return isset($this->coinable) ? $this->coinable->getCoinableType() : '';
    }

    public function getCoinableObjectAttribute()
    {
        return isset($this->coinable) ? $this->coinable->getCoinableObject() : '';
    }

    public static function typeLabels()
    {
        return [
            self::TYPE_HEART => 'Heart Coin',
            self::TYPE_BRAIN => 'Brain Coin',
            self::TYPE_QUIZ => 'Quiz',
            self::TYPE_COUPON => 'Coupon Redemption',
            self::TYPE_MISSION => 'Mission',
            self::TYPE_ADMIN => 'Admin Bonus',
            self::TYPE_QUERY => 'Query',
            self::TYPE_VISION => 'Vision',
            self::TYPE_ASSIGN_TASK => 'Assigned Task',
            self::TYPE_CORRECT_SUBMISSION => 'Correct Submission',
        ];
    }
}
