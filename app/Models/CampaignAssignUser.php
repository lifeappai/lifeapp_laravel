<?php

namespace App\Models;

use App\Interfaces\Coinable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CampaignAssignUser
 * @package App\Models
 *
 * @property User $user
 */
class CampaignAssignUser extends Model implements Coinable
{
    protected $table = 'campaign_assign_user';

    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'user_id',
        'coins',
        'given_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @param $coins
     */
    public function setCoins($coins)
    {
        $this->coins = $coins;
        $this->given_at = Carbon::now()->toDateTimeString();
        $this->save();
    }

    /**
     * @return string
     */
    public function getCoinableType()
    {
        return "Campaign";
    }

    /**
     * @return array
     */
    public function getCoinableObject()
    {
        return $this->user->getCoinableObject();
    }
}
