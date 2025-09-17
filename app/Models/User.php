<?php

namespace App\Models;

use App\Enums\UserType;
use App\Http\Resources\MissionResource;
use App\Http\Resources\TopicResource;
use App\Interfaces\Coinable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

/**
 * Class User
 * @package App\Models
 *
 * @property integer id
 * @property string device_token
 * @property float heart_coins
 * @property float brain_coins
 * @property string name
 * @property int type
 * @property float earn_coins
 */





class User extends Authenticatable implements Coinable
{
    use HasApiTokens, HasFactory, Notifiable;

    

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'pin',
        'school_id',
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
        'image_path',
        'otp',
        'device',
        'device_token',
        'earn_coins',
        'la_board_id',
        'board_name',
        'guardian_name',
        'la_section_id',
        'la_grade_id',
        'created_by',
        'school_code',
        'user_rank'
    ];

    protected $appends = [
        'user_rank'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function getSchoolAddress()
    {
        if (!$this->school) {
            return null;
        }

        return implode(', ', array_filter([
            $this->school->name,
            $this->school->block,
            $this->school->cluster,
            $this->school->city,
            $this->school->district,
            $this->school->state,
            $this->school->pin_code
        ]));
    }

    public function laGrade()
    {
        return $this->belongsTo(LaGrade::class, 'la_grade_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function laSection()
    {
        return $this->belongsTo(LaSection::class, 'la_section_id');
    }

    public function couponRedeems(): HasMany
    {
        return $this->hasMany(CouponRedeem::class, 'user_id');
    }

    public function laTeacherGrades(): HasMany
    {
        return $this->hasMany(LaTeacherGrade::class, 'user_id');
    }

    public function missionCoutByUser($userid)
    {
        return MissionComplete::where('user_id', '=', $userid)
            ->whereNotNull('approved_at')->count();
    }

    public function movieCoutByUser($userid)
    {
        return MovieUserTiming::where('user_id', '=', $userid)->count();
    }

    public function getPointsAttribute()
    {
        $points = DB::table('movie_completes')
            ->where(['user_id' => $this->id])->average('avg_rating');

        $mission_points = DB::table('user_mission_completes')
            ->where('user_id', $this->id)->average('rating');

        $divider = (is_null($points) ? 0 : 1) + (is_null($mission_points) ? 0 : 1);
        $divider = $divider == 0 ? 1 : $divider;

        $total_rating = ($points + $mission_points) / $divider;

        return [
            "total_earn_points" => $this->brain_coins + $this->heart_coins,
            "total_brain_points" => $this->brain_coins,
            "total_heart_points" => $this->heart_coins,
            "total_rating" => (int)$total_rating,
        ];
    }

    public function points()
    {
        $points = MovieComplete::select(
            DB::raw("SUM(brain_points) as brain_point"),
            DB::raw("SUM(heart_points) as heart_point"),
            DB::raw("SUM(earn_points) as earn_points"),
            DB::raw('AVG(avg_rating) as rating')
        )->where(['user_id' => $this->id])->get();

        $mission_points = UserMissionComplete::select(
            DB::raw("SUM(if(mission_type = 'brain', earn_points, 0)) as brain_point"),
            DB::raw("SUM(if(mission_type = 'heart', earn_points, 0)) as heart_point"),
            DB::raw("SUM(earn_points) as earn_points"),
            DB::raw('AVG(rating) as rating')
        )->where(['user_id' => $this->id])->get();

        $total_earn_points = $points[0]->earn_points + $mission_points[0]->earn_points;
        $total_brain_points = $points[0]->brain_point + $mission_points[0]->brain_point;
        $total_heart_points = $points[0]->heart_point + $mission_points[0]->heart_point;

        $divider = (is_null($points[0]->rating) ? 0 : 1) + (is_null($mission_points[0]->rating) ? 0 : 1);
        $divider = $divider == 0 ? 1 : $divider;

        $total_rating = ($points[0]->rating + $mission_points[0]->rating) / $divider;

        return [
            "total_earn_points" => $total_earn_points,
            "total_brain_points" => $total_brain_points,
            "total_heart_points" => $total_heart_points,
            "total_rating" => $total_rating
        ];
    }

    public function getActiveMission()
    {
        return Mission::whereDoesntHave('missionCompletes', function ($completed) {
            return $completed->where('user_id', $this->id)
                ->whereNotNull('approved_at');
        })->first();
    }

    public function getActiveTopic()
    {
        $topicsIds = Movie::select('topic_id')->wherehas('completed', function ($moveComplete) {
            return $moveComplete->where('user_id', $this->id);
        })->pluck('topic_id')->toArray();

        return Topics::whereNotIn('id', array_values($topicsIds))->orderBy('id', 'ASC')->first();
    }

    /**
     * @return HasMany | Campaign
     */
    public function campaigns()
    {
        return $this->hasMany(Campaign::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function requestingCampaigns()
    {
        return $this->belongsToMany(Campaign::class, 'campaign_assign_user', 'user_id', 'campaign_id');
    }

    public function friends()
    {
        return $this->belongsToMany(User::class, 'friendships', 'sender_id', 'recipient_id')
            ->wherePivot('status', 'confirmed')->wherePivot('deleted_at', null);
    }

    public function friendsAccept()
    {
        return $this->belongsToMany(User::class, 'friendships', 'recipient_id', 'sender_id')
            ->wherePivot('status', 'confirmed')->wherePivot('deleted_at', null);
    }

    public function friendRequests()
    {
        return $this->belongsToMany(User::class, 'friendships', 'recipient_id', 'sender_id',)
            ->wherePivot('status', 'pending')->wherePivot('deleted_at', null);
    }

    public function myFriendRequests()
    {
        return $this->belongsToMany(User::class, 'friendships', 'sender_id', 'recipient_id')
            ->wherePivot('status', 'pending')->wherePivot('deleted_at', null);
    }

    /**
     * @return HasMany
     */
    public function missionCompletes()
    {
        return $this->hasMany(MissionComplete::class, 'user_id');
    }

    /**
     * @return HasMany
     */
    public function laMissionCompletes()
    {
        return $this->hasMany(LaMissionComplete::class, 'user_id');
    }

    /**
     * @return HasMany
     */
    public function laQuizGameResults()
    {
        return $this->hasMany(LaQuizGameResult::class, 'user_id');
    }

    /**
     * @return HasMany
     */
    public function missionApproved()
    {
        return $this->hasMany(MissionComplete::class, 'user_id')->whereNotNull('approved_at');
    }


    /**
     * @return HasMany
     */
    public function laMissionApproved()
    {
        return $this->hasMany(LaMissionComplete::class, 'user_id')
                    ->whereNotNull('approved_at');
    }

    /**
     * @return HasMany
     */
    public function missionRequests()
    {
        return $this->hasMany(MissionComplete::class, 'user_id')->where('approved_at', null)->where('rejected_at', null);
    }

    /**
     * @return HasMany
     */
    public function laMissionRequests()
    {
        return $this->hasMany(LaMissionComplete::class, 'user_id')
                    ->whereNotNull('created_at'); // Only count missions that were ever submitted
    }

    /**
     * @return HasMany
     */
    public function laMissionRejected()
    {
        return $this->hasMany(LaMissionComplete::class, 'user_id')
                    ->whereNotNull('rejected_at');
    }

    /**
     * @return HasMany
     */
    public function missionUploads()
    {
        return $this->hasMany(MissionUpload::class, 'user_id');
    }

    /**
     * @return bool
     */
    public function canSendNotification()
    {
        return !empty($this->device_token);
    }

    public function createTransaction($object, $coins, $type)
    {
        $transaction = new CoinTransaction([
            'user_id' => $this->id,
            'amount' => $coins,
            'type' => $type,
        ]);

        $transaction->attachObject($object);
    }

    public function coinTransactions()
    {
        return $this->hasMany(CoinTransaction::class, 'user_id');
    }

    /**
     * @return string
     */
    public function getCoinableType()
    {
        return "User";
    }

    /**
     * @return array
     */
    public function getCoinableObject()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'profile_image' => $this->profile_image ?? null,
        ];
    }

    public function mentorSubjects(): HasMany
    {
        return $this->hasMany(MentorSubject::class, 'user_id');
    }

    public function laSubjectCouponCodes(): HasMany
    {
        return $this->hasMany(LaSubjectCouponCode::class, 'user_id');
    }

    public function laQueries()
    {
        return $this->hasMany(LaQuery::class, 'created_by');
    }

    /**
     * @return bool
     */
    public function isMentor()
    {
        return $this->type == UserType::Mentor;
    }

    /**
     * @return bool
     */
    public function isStudent()
    {
        return $this->type == UserType::Student;
    }

    public function isTeacher()
    {
        return $this->type == UserType::Teacher;
    }


    /**
     * @return HasMany
     */
    public function assignedQueries()
    {
        return $this->hasMany(LaQuery::class, 'mentor_id');
    }

    public function earnCoinsByType($type)
    {
        $coinType = CoinTransaction::TYPE_MISSION;
        if ($type == "quiz") {
            $coinType = CoinTransaction::TYPE_QUIZ;
        }
        $totalAmount = CoinTransaction::where('type', $coinType)->where('user_id', $this->id)->sum('amount');
        return $totalAmount;
    }

    public function scopeFilterUser($query, $data)
    {
        $query->orderBy('id', 'desc');

        if (isset($data['mobileNumber'])) {
            $users = $query->where('mobile_no', 'like', "%" . $data['mobileNumber'] . "%");
        }

        if (isset($data['state'])) {
            $query = $query->where('state', $data['state']);
        }

        if (isset($data['city'])) {
            $query = $query->where('city', $data['city']);
        }

        if (isset($data['type'])) {
            $query = $query->where('type', $data['type']);
        }

        if (isset($data['grade'])) {
            $query = $query->where('grade', $data['grade']);
        }
        if (isset($data['register_date']) && isset($data['register_end_date'])) {
            $date = date("Y-m-d", strtotime($data['register_date']));
            $endDate = date("Y-m-d", strtotime($data['register_end_date']));
            $query = $query->whereDate('created_at', '>=', $date)
                ->whereDate('created_at', '<=', $endDate);
        } else if (isset($data['register_date'])) {
            $date = date("Y-m-d", strtotime($data['register_date']));
            $query = $query->whereDate('created_at', $date);
        } else if (isset($data['register_end_date'])) {
            $endDate = date("Y-m-d", strtotime($data['register_end_date']));
            $query = $query->whereDate('created_at', $endDate);
        }

        if (isset($data['earn_coins'])) {
            $earnCoins = $data['earn_coins'];
            if ($earnCoins == 1) {
                $query = $query->where('earn_coins', '<=', 1000);
            }
            if ($earnCoins == 2) {
                $query = $query->whereBetween('earn_coins', [1001, 5000]);
            }
            if ($earnCoins == 3) {
                $query = $query->whereBetween('earn_coins', [5000, 10000]);
            }
            if ($earnCoins == 4) {
                $query = $query->where('earn_coins', '>', 10001);
            }
        }

        if (isset($data['school_id'])) {
            $query = $query->whereHas('school', function ($query) use ($data) {
                $query->where('school_id', $data['school_id']);
            });
        }




        //school filter
        if (isset($data['school_name'])) {
            $query = $query->whereHas('school', function ($query) use ($data) {
                $query->where('name', 'LIKE', '%' . $data['school_name'] . '%');
            });
        }
        



        if (isset($data['missionType'])) {
            $missionType  = $data['missionType'];
            if ($missionType == "approved") {
                $query->whereHas('laMissionApproved');
            } else if ($missionType == "requested") {
                $query->whereHas('laMissionRequests');
            }
        }


        // Count all missions that were ever requested (submitted)
        if (isset($data['mission_requested'])) {
            $query->withCount('laMissionRequests')->having('la_mission_requests_count', $data['mission_requested']);
        }

        // Count only missions that were approved
        if (isset($data['mission_approved'])) {
            $query->withCount('laMissionApproved')->having('la_mission_approved_count', $data['mission_approved']);
        }

        // Count only missions that were rejected
        if (isset($data['mission_rejected'])) {
            $query->withCount('laMissionRejected')->having('la_mission_rejected_count', $data['mission_rejected']);
        }

        if (isset($data['district_name'])) {
            $query = $query->whereHas('school', function ($query) use ($data) {
                $query->where('district', $data['district_name']);
            });
        }

        if (isset($data['block_name'])) {
            $query = $query->whereHas('school', function ($query) use ($data) {
                $query->where('block', $data['block_name']);
            });
        }

        if (isset($data['cluster_name'])) {
            $query = $query->whereHas('school', function ($query) use ($data) {
                $query->where('cluster', $data['cluster_name']);
            });
        }

        if (isset($data['school_code'])) {
            $query = $query->whereHas('school', function ($query) use ($data) {
                $query->where('code', $data['school_code']);
            });
        }

        return $query;
    }
}
