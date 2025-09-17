<?php

namespace App\Models;

use App\Enums\NotificationAction;
use App\Notifications\SendPushNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PushNotificationCampaign
 * @package App\Models
 *
 * @property integer id
 * @property integer created_by
 *
 * @property string name
 * @property string title
 * @property string body
 * @property string media_id
 *
 * @property string school_id
 * @property string city
 * @property string state
 *
 * @property array users
 * @property array success_users
 * @property array failed_users
 *
 * @property Carbon scheduled_date
 * @property Carbon scheduled_at
 * @property Carbon completed_at
 *
 * @property Media media
 *
 */
class PushNotificationCampaign extends Model
{
    use HasFactory;

    protected $table = 'push_notification_campaigns';

    protected $fillable = [
        'name',
        'title',
        'body',

        'school_id',
        'city',
        'state',
    ];

    protected $casts = [
        'users' => 'array',
        'success_users' => 'array',
        'failed_users' => 'array',
    ];

    public function notificationSent($user)
    {
        $users = $this->success_users;
        $users[] = $user->id;
        $this->success_users = $users;
        $this->save();
    }

    public function notificationFailed($user)
    {
        $users = $this->failed_users;
        $users[] = $user->id;
        $this->failed_users = $users;
        $this->save();
    }

    public function schedule()
    {
        $this->scheduled_at = Carbon::now()->toDateTimeString();
        $this->save();

        User::whereIn('id', $this->users)->chunk(100, function ($users) {

            foreach ($users as $user) {
                $pushNotification = new SendPushNotification(
                    $this->title,
                    $this->body,
                    $user->device_token ? [$user->device_token] : [],
                    [
                        'action' => NotificationAction::AdminCampaign(),
                        'action_id' => $this->id,
                        'media_url' => $this->media ? $this->media->path : null
                    ]
                );

                $user->notify($pushNotification);
            }

            return true;
        });
    }

    public function setUsers($filter)
    {
        $users = User::query();
        if (isset($filter['city'])) {
            $users->where('city', $filter['city']);
        }
        if (isset($filter['school_id'])) {
            $users->where('school_id', $filter['school_id']);
        }
        if (isset($filter['state'])) {
            $users->where('state', $filter['state']);
        }

        if (isset($filter['mobileNumber'])) {
            $users = $users->where('mobile_no', 'like', "%" . $filter['mobileNumber'] . "%");
        }

        if (isset($filter['grade'])) {
            $users = $users->where('grade', $filter['grade']);
        }

        if (isset($filter['registerDate'])) {
            $date =  date("Y-m-d", strtotime($filter['registerDate']));
            $users = $users->whereDate('created_at', $date);
        }

        if (isset($filter['earnCoins'])) {
            if ($filter['earnCoins'] == 1) {
                $users = $users->where('earn_coins', '<=', 1000);
            }
            if ($filter['earnCoins'] == 2) {
                $users = $users->whereBetween('earn_coins', [1001, 5000]);
            }
            if ($filter['earnCoins'] == 3) {
                $users = $users->where('earn_coins', '>', 5000);
            }
        }

        if (isset($filter['name'])) {
            $users->where('name', $filter['name']);
        }

        if (isset($filter['mobile_no'])) {
            $users->where('mobile_no', $filter['mobile_no']);
        }

        $missionType = $filter['missionType'];

        if ($missionType == "approved") {
            $users->whereHas('laMissionApproved');
        } else if ($missionType == "requested") {
            $users->whereHas('laMissionRequests');
        }

        if (isset($filter['missionRequested'])) {
            $users->withCount('laMissionRequests')->having('la_mission_requests_count', $filter['missionRequested']);
        }

        if (isset($filter['missionApproved'])) {
            $users->withCount('laMissionApproved')->having('la_mission_approved_count', $filter['missionApproved']);
        }

        $this->users = $users->pluck('id')->toArray();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }
}
