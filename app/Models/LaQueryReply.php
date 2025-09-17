<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LaQueryReply
 * @package App\Models
 *
 * @property int $id
 * @property int $user_id
 * @property string text
 * @property int $media_id
 * @property int $la_query_id
 *
 * @property LaQuery $laQuery
 * @property User $user
 * @property Media $media
 */
class LaQueryReply extends Model
{
    use HasFactory;

    protected $table = "la_query_replies";

    protected $fillable = [
        'user_id',
        'text',
        'media_id'
    ];

    public function laQuery()
    {
        return $this->belongsTo(LaQuery::class, 'la_query_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function media()
    {
        return $this->belongsTo(Media::class, 'media_id');
    }
}
