<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Campaign
 * @package App\Models
 *
 * @property int coins
 * @property string title
 * @property Carbon|mixed completed_at
 */
class Campaign extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'coupon_id',
        'title',
        'coins',
        'reason',
        'friend_request',
    ];

    protected $casts = [
        'friend_request' => 'array',
    ];

    /**
     * @return BelongsTo
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

    /**
     * @return HasMany
     */
    public function student($userID)
    {
        return User::where(['id' => $userID])->first();
    }

    /**
     * @return BelongsTo | User
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany | User
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'campaign_assign_user', 'campaign_id', 'user_id');
    }

    /**
     * @return HasMany | CampaignAssignUser
     */
    public function givenCoins()
    {
        return $this->hasMany(CampaignAssignUser::class);
    }

    public function completed()
    {
        $this->completed_at = Carbon::now();
        $this->save();
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopePending($query)
    {
        return $query->whereNull('completed_at');
    }
}
