<?php

namespace App\Models;

use App\Interfaces\Coinable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class VisionQuestionAnswer
 * @package App\Models
 *
 * @property int id
 * @property int vision_id
 * @property int question_id
 * @property int user_id
 * @property string|null answer_text
 * @property string|null answer_option
 * @property int|null media_id
 * @property string|null description
 * @property string answer_type // 'text', 'option', 'image'
 * @property string timing
 * @property Carbon approved_at
 * @property Carbon rejected_at
 * @property string comment
 */
class VisionQuestionAnswer extends Model implements Coinable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'user_id',
        'vision_id',
        'question_id',
        'answer_option',
        'answer_text',
        'media_id',
        'description',
        'answer_type',
        'timing',
        'score',
        'is_first_attempt',
        'attempt_number',
        'status',
        'comment',
    ];

    protected $dates = [
        'approved_at',
        'rejected_at',
    ];

    protected $appends = [
        'status_code',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vision(): BelongsTo
    {
        return $this->belongsTo(Vision::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(VisionQuestion::class, 'question_id');
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    public function approved($comment = null)
    {
        $this->approved_at = now();
        $this->rejected_at = null;
        $this->comment = $comment;
        $this->status = 'approved';
        $this->score = $this->getPoints();
        $this->save();
    }

    public function rejected($comment = null)
    {
        $this->rejected_at = now();
        $this->approved_at = null;
        $this->comment = $comment;
        $this->status = 'rejected';
        $this->save();
    }

    public function getStatusCodeAttribute()
    {
        if ($this->rejected_at) return -1;
        if ($this->approved_at) return 1;
        return 0;
    }

    public function isInReview()
    {
        return !$this->approved_at && !$this->rejected_at;
    }

    public function isApproved()
    {
        return $this->approved_at != null;
    }

    public function isRejected()
    {
        return $this->rejected_at != null;
    }

    // Optional if MCQs give coins immediately
    public function getCoinableType()
    {
        return $this->vision->getCoinableType();
    }

    public function getCoinableObject()
    {
        return $this->vision->getCoinableObject();
    }

    public function getPoints()
    {
        $vision = $this->vision; // load the related vision
        $level  = $vision?->laLevel;

        if (!$level) return 0;

        if ($this->answer_type === 'option') {
            return $level->vision_mcq_points ?? 0;
        } elseif (in_array($this->answer_type, ['text', 'image'])) {
            return $level->vision_text_image_points ?? 0;
        }
        return 0;
    }
    
}
