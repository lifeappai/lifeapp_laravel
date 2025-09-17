<?php

namespace App\Models;

use App\Interfaces\Coinable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class MissionComplete
 * @package App\Models
 *
 * @property int id
 * @property Carbon approved_at
 * @property Carbon rejected_at
 * @property string comment
 * @property int rating
 * @property Mission mission
 * @property User user
 */
class MissionComplete extends Model implements Coinable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'mission_id',
        'user_id',
        'description',
    ];

    protected $appends = [
        'status',
    ];

   /**
     * @return BelongsTo
     */
    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class, 'mission_id');
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approved($comment = null)
    {
        $this->approved_at = Carbon::now();
        $this->rejected_at = null;
        $this->comment = $comment;
        $this->save();
    }

    public function rejected($comment = null)
    {
        $this->rejected_at = Carbon::now();
        $this->approved_at = null;
        $this->comment = $comment;
        $this->save();
    }

    public function getStatusAttribute()
    {
        if (!is_null($this->rejected_at)) {
            return -1;
        }

        if (!is_null($this->approved_at)) {
            return 1;
        }
        return 0;
    }

    /**
     * @return bool
     */
    public function isInReview()
    {
        return $this->approved_at == null && $this->rejected_at == null;
    }

    /**
     * @return bool
     */
    public function isApproved()
    {
        return $this->approved_at != null;
    }

    /**
     * @return bool
     */
    public function isRejected()
    {
        return $this->rejected_at != null;
    }

    public function getCoinableType()
    {
        return $this->mission->getCoinableType();
    }

    public function getCoinableObject()
    {
        return $this->mission->getCoinableObject();
    }
}
