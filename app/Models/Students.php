<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Students extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'school_id',
        'user_id',
        'dob',
        'gender',
        'grade',
        'address',
        'mobile_no',
        'state',
        'city',
        'school_name',
        'type',
        'profile_image',
        'image_path'
    ];
    /**
     * @return HasMany
     */
    public function students() : hasMany
    {
        return $this->hasMany(School::class,'id','school_id');
    }

    public function friends()
	{
		return $this->belongsToMany(Students::class, 'friendships', 'sender_id', 'recipient_id')->wherePivot('status', 'confirmed');
	}

	public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
