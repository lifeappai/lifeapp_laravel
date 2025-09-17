<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'state',
        'city',
        'pin_code',
        'status',
        'app_visible',
        'is_life_lab',
        'code',
        'district',
        'block',
        'cluster'
    ];

    /**
     * @return HasMany
     */
    public function students(): HasMany
    {
        return $this->hasMany(Students::class, 'school_id');
    }


    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'school_id', 'id');
    }

    public function scopeFilterSchool($query, $data)
    {
        if (isset($data['school_name'])) {
            $query = $query->where('name', 'like', "%" .  $data['school_name'] . "%");
        }

        if (isset($data['status'])) {
            $query = $query->where('status', $data['status']);
        }

        if (isset($data['state'])) {
            $query = $query->where('state', $data['state']);
        }

        if (isset($data['city'])) {
            $query = $query->where('city', $data['city']);
        }

        if (isset($data['code'])) {
            $query = $query->where('code', $data['code']);
        }

        if (isset($data['district'])) {
            $query = $query->where('district', $data['district']);
        }

        $query->orderBy('id', 'desc');
        return $query;
    }

    public function getDistrictCount($district, $state, $createdAt = null, $laMissionId = null)
    {
        $query = $this->where('status', StatusEnum::ACTIVE)
            ->where('app_visible', StatusEnum::ACTIVE)
            ->where('state', $state)
            ->where('district', $district);

        if ($createdAt !== null) {
            $query->whereHas('users', function ($subQuery) use ($state, $createdAt, $laMissionId) {
                $subQuery->where('state', $state)
                    ->where('created_at', '>', $createdAt);

                if ($laMissionId !== null) {
                    $subQuery->whereHas('laMissionCompletes', function ($subSubQuery) use ($laMissionId) {
                        $subSubQuery->where('la_mission_id', $laMissionId);
                    });
                }
            });
        }

        $count = $query->count();
        return $count;
    }

    public function getUserCount($district, $state, $createdAt)
    {
        return $this->where('district', $district)->whereHas('users', function ($subQuery) use ($state, $createdAt) {
            $subQuery->where('state', $state)
                ->where('created_at', '>', $createdAt);
        })->count();
    }
}
