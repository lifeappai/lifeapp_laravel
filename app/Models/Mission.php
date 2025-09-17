<?php

namespace App\Models;

use App\Constants\NotificationTemplate;
use App\Enums\NotificationAction;
use App\Http\Traits\LocaleTrait;
use App\Interfaces\Coinable;
use App\Notifications\SendPushNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

/**
 * @property integer brain_points
 * @property integer heart_points
 * @property string mission_type
 * @property string mission_name
 */
class Mission extends Model implements Coinable
{
    use HasFactory, Notifiable, LocaleTrait, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'mission_name',
        'mission_type',
        'locale',
        'flag',
        'brain_points',
        'heart_points'
    ];

    /**
     * @return HasMany
     */
    public function missionQuestions() : HasMany
    {
        return $this->hasMany(MissionQuestionTranslation::class,'mission_id');
    }
    /**
     * @return HasMany
     */
    public function missionImages() : HasMany
    {
        return $this->hasMany(MissionImage::class,'mission_id');
    }

    public function getUserMissionTimings($userId)
    {
        return MissionUserTiming::where('user_id',$userId)->where('mission_id',$this->id)->groupBy('mission_img_id')->get();
    }

    public function getUserMissionUpload($userId)
    {
        return MissionUpload::where('user_id',$userId)->where('mission_id',$this->id)->latest()->first();
    }

    public function getUserMissionComplete($userId)
    {
        return MissionComplete::where('user_id',$userId)->where('mission_id',$this->id)->latest()->first();
    }

    public function getUserApproveMissionComplete($userId)
    {
        return UserMissionComplete::where('user_id',$userId)->where('mission_id',$this->id)->latest()->first();
    }

    
    public function getLocalWiseMissionImages($locale)
    {
        return MissionImage::where('locale',$locale)->where('mission_id',$this->id)->get();
    }

    public function getMissionQuestionDocument($locale)
    {
        return MissionQuestionTranslation::where('locale',$locale)->where('mission_id',$this->id)->latest()->first();
    }

    public function isActive($user)
    {
        $missionCompleted = $user->missionCompletes()->whereNotNull('approved_at')->pluck('mission_id')->toArray();
        $missionCompleted = array_values($missionCompleted);
        if (in_array($this->id, $missionCompleted)) {
            return 1;
        }

        $active = Mission::whereNotIn('id', $missionCompleted)->orderBy('id', 'ASC')->first();

        return $this->id == $active->id;
    }

    /**
     * @return HasMany | MissionComplete
     */
    public function missionCompletes()
    {
        return $this->hasMany(MissionComplete::class, 'mission_id');
    }

    /**
     * @return HasMany | MissionUpload
     */
    public function missionUploads()
    {
        return $this->hasMany(MissionUpload::class, 'mission_id');
    }

    /**
     * @param $value
     */
    public function setMissionNameAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['mission_name'] = json_encode($value);
            return;
        }
        $this->attributes['mission_name'] = $value;
    }

    /**
     * @param $value
     * @return array
     */
    public function getMissionNameAttribute($value)
    {
        $check = json_decode($value, true);
        if (is_array($check)) {
            return $check;
        }
        return $value;
    }

    /**
     * @param $locale
     * @param $data
     * @return Model
     */
    public function createQuestionDocument($locale, $data)
    {
        $image = $data['document'];

        $mediaName = $image->getClientOriginalName();
        $mediaPath = Storage::put('media', $image );
        $media = Media::create([
            'name'    => $mediaName,
            'path'    => $mediaPath
        ]);

        return $this->missionQuestions()->create([
            'locale' => $locale,
            'question_title' => $data['title'],
            'question_media_id'  => $media->id,
        ]);
    }

    /**
     * @param $locale
     * @param $image
     * @return Model
     */
    public function createImage($locale, $image)
    {
        $mediaName = $image->getClientOriginalName();
        $mediaPath = Storage::put('media', $image);
        $media = Media::create([
            'name' => $mediaName,
            'path' => $mediaPath
        ]);

        return $this->missionImages()->create([
            'locale' => $locale,
            'mission_media_id' => $media->id,
        ]);
    }

    public function updateQuestion($locale, $data)
    {
        if (isset($data['document'])) {
            $image = $data['document'];
            $mediaName = $image->getClientOriginalName();
            $mediaPath = Storage::put('media', $image);
            $media = Media::create([
                'name'    => $mediaName,
                'path'    => $mediaPath
            ]);
            $mediaId = $media->id;
        } else {
            $missionQuestion = MissionQuestionTranslation::where('locale',$locale)->where('mission_id',$this->id)->latest()->first();
            $mediaId = "";
            if($missionQuestion)
            {
                $mediaId = $missionQuestion->question_media_id;
            }
        }
        $this->missionQuestions()->updateOrCreate([
            'locale' => $locale,
        ], [
            'question_title' => $data['title'],
            'question_media_id' => $mediaId,
        ]);
    }

    /**
     * @return integer
     */
    public function getPoints()
    {
        return $this->type === "brain" ? $this->brain_points : $this->heart_points;
    }

    public function getLocaleName($locale)
    {
        $missionName = $this->mission_name;
        if (is_array($missionName)) {
            if (isset($missionName[$locale])) {
                return $missionName[$locale];
            }
            return count($missionName) > 0 ? array_values($missionName)[0] : "";
        }
        return $missionName;
    }

    public function sendNotification()
    {
        User::orderBy('id', 'asc')->chunk(50, function ($users) {
            $tokens = $users->whereNotNull('device_token')->pluck('device_token')->toArray();
            $notification = NotificationTemplate::NEW_MISSION;

            $payload = [
                'action' => NotificationAction::Mission(),
                'action_id' => $this->id,
            ];

            $pushNotification = new SendPushNotification(
                $notification['title'],
                $notification['body'],
                $tokens,
                $payload
            );
            Notification::send($users, $pushNotification);
        });
    }

    public function sendApproveNotification(User $toUser)
    {
        $tokens = $toUser->canSendNotification() ? [$toUser->device_token] : [];
        $notification = NotificationTemplate::MISSION_APPROVED;
        $payload = [
            'action' => NotificationAction::Mission(),
            'action_id' => $this->id
        ];

        $pushNotification = new SendPushNotification(
            $notification['title'],
            $notification['body'],
            $tokens,
            $payload
        );

        $toUser->notify($pushNotification);
    }

    public function sendRejectNotification(User $toUser)
    {
        $tokens = $toUser->canSendNotification() ? [$toUser->device_token] : [];
        $notification = NotificationTemplate::MISSION_REJECTED;
        $payload = [
            'action' => NotificationAction::Mission(),
            'action_id' => $this->id
        ];

        $pushNotification = new SendPushNotification(
            $notification['title'],
            $notification['body'],
            $tokens,
            $payload
        );

        $toUser->notify($pushNotification);
    }

    public function getCoinableType()
    {
        return "Mission";
    }

    public function getCoinableObject()
    {
        return [
            'id' => $this->id,
            'name' => $this->getLocaleName("en")
        ];
    }
}
