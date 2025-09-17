<?php

namespace App\Models;

use App\Constants\NotificationTemplate;
use App\Enums\GameType;
use App\Enums\NotificationAction;
use App\Http\Resources\API\V1\MediaResource;
use App\Notifications\SendPushNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Notification;
use \Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

class LaMission extends Model
{
    use HasFactory;

    public $fillable = [
        "title",
        "description",
        "image",
        "question",
        "document",
        "la_subject_id",
        "la_level_id",
        "type",
        "allow_for",
        "status"
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'image' => 'array',
        'question' => 'array',
        'document' => 'array',
    ];

    protected $appends = [
        'default_title'
    ];

    public function getDefaultTitleAttribute()
    {
        foreach ($this->title as $value) {
            return $value;
        }
        return '';
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(LaSubject::class, 'la_subject_id');
    }

    public function laLevel(): BelongsTo
    {
        return $this->belongsTo(LaLevel::class, 'la_level_id');
    }

    public function laMissionResources(): HasMany
    {
        return $this->hasMany(LaMissionResource::class, 'la_mission_id');
    }

    public function missionCompletes()
    {
        return $this->hasMany(LaMissionComplete::class, 'la_mission_id');
    }

    public function laMissionAssigns()
    {
        return $this->hasMany(LaMissionAssign::class, 'la_mission_id');
    }

    public static function getMediaPath($mediaId)
    {
        return Media::where('id', $mediaId)->first();
    }

    public static function getMediaResource($mediaId)
    {
        $media = Media::where('id', $mediaId)->first();
        return new MediaResource($media);
    }

    public function sendApproveNotification(User $toUser)
    {
        $tokens = $toUser->canSendNotification() ? [$toUser->device_token] : [];
        $notification = NotificationTemplate::MISSION_APPROVED;
        $payload = [
            'action' => NotificationAction::Mission(),
            'action_id' => $this->id,
            'la_subject_id' => $this->la_subject_id ?? '',
            'la_level_id' => $this->la_level_id ?? '',
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
            'action_id' => $this->id,
            'la_subject_id' => $this->la_subject_id ?? '',
            'la_level_id' => $this->la_level_id ?? '',
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
            'title' => $this->default_title
        ];
    }

    public function getGamePoints()
    {
        if ($this->type == GameType::MISSION) {
            return $this->laLevel->mission_points ?? 0;
        } elseif ($this->type == GameType::JIGYASA) {
            return $this->laLevel->jigyasa_points ?? 0;
        } elseif ($this->type == GameType::PRAGYA) {
            return $this->laLevel->pragya_points ?? 0;
        }
    }

    public function sendAssignedMissionNotification($userIds)
    {
        User::whereIn('id', $userIds)
            ->orderBy('id', 'asc')
            ->chunk(50, function ($users) {
                $notification = NotificationTemplate::NEW_MISSION_ASSIGNED;

                foreach ($users as $user) {
                    if (!$user->device_token) {
                        continue;
                    }

                    $payload = [
                        'action'        => NotificationAction::Mission(),
                        'mission_id'    => $this->id ?? '',
                        'la_subject_id' => $this->la_subject_id ?? '',
                        'la_level_id'   => $this->la_level_id ?? '',
                    ];

                    $pushNotification = new SendPushNotification(
                        $notification['title'],
                        $notification['body'],
                        [$user->device_token], // only this user’s token
                        $payload
                    );

                    Notification::send($user, $pushNotification);
                }
            });

        return true;
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class, 'chapter_id');
    }

}