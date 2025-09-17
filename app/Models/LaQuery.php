<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LaQuery
 * @package App\Models
 *
 * @property int $id
 * @property int $created_by
 * @property int $mentor_id
 * @property int $la_subject_id
 * @property string $description
 * @property int $media_id
 * @property string feedback
 * @property int rating
 *
 * @property User $createdBy
 * @property User $mentor
 * @property Media $media
 * @property LaSubject $subject
 * @property string status
 * @property int status_id
 * @property int waiting_reply
 */
class LaQuery extends Model
{
    use HasFactory;

    protected $table = "la_queries";

    const STATUS_OPEN = 0;

    const STATUS_CLOSED = 1;

    protected $fillable = [
        'la_subject_id',
        'description',
        'media_id',
        'mentor_id',
        'status_id',
    ];

    protected $appends = [
        'status'
    ];

    protected $hidden = [
        'la_subject_id',
        'media_id',
        'mentor_id',
        'status_id',
    ];

    public function getStatusAttribute() : string
    {
        switch ($this->status_id) {
            case self::STATUS_CLOSED:
                return 'closed';
            default:
                return 'open';
        }
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function media()
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

    public function laReplies()
    {
        return $this->hasMany(LaQueryReply::class, 'la_query_id');
    }

    public function subject()
    {
        return $this->belongsTo(LaSubject::class, 'la_subject_id');
    }

    /**
     * @return bool
     */
    public function isClosed()
    {
        return $this->status_id == self::STATUS_CLOSED;
    }

    public function coinsForMentor()
    {
        if ($this->rating == 1) {
            return random_int(1, 20);
        }

        if ($this->rating == 2) {
            return random_int(20, 40);
        }

        if ($this->rating == 3) {
            return random_int(40, 60);
        }

        if ($this->rating == 4) {
            return random_int(60, 80);
        }

        return random_int(80, 100);
    }
}
