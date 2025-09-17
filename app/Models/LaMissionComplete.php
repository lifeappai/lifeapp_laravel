<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaMissionComplete extends Model
{
    use HasFactory;

    protected $fillable = [
        "la_mission_id",
        "user_id",
        "media_id",
        "description",
        "comments",
        "points",
        "timing",
        "status",
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo
     */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

    /**
     * @return BelongsTo
     */
    public function laMission(): BelongsTo
    {
        return $this->belongsTo(LaMission::class, 'la_mission_id');
    }


    public function approved($comment = null)
    {
        $this->approved_at = Carbon::now();
        $this->rejected_at = null;
        $this->comments = $comment;
        $this->save();
    }

    public function rejected($comment = null)
    {
        $this->rejected_at = Carbon::now();
        $this->approved_at = null;
        $this->comments = $comment;
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
     * @return string
     */
    public function getCoinableType()
    {
        return $this->laMission->getCoinableType();
    }

    /**
     * @return array
     */
    public function getCoinableObject()
    {
        return $this->laMission->getCoinableObject();
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

    public function laMissionAssign()
    {
        return $this->hasOne(LaMissionAssign::class, 'user_id', 'user_id');
    }

    public function markSubmitted($comment = null)
    {
        $this->status = 'submitted';
        $this->comments = $comment;
        $this->save();
    }

    public function markCompleted($comment = null)
    {
        $this->status = 'completed';
        $this->comments = $comment;
        $this->save();
    }

    public function markRejected($comment = null)
    {
        $this->status = 'rejected';
        $this->comments = $comment;
        $this->save();
    }

    public function markSkipped($comment = null)
    {
        $this->status = 'skipped';
        $this->comments = $comment;
        $this->save();
    }

    // ---------------- Helpers ----------------
    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isSkipped(): bool
    {
        return $this->status === 'skipped';
    }
}
