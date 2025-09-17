<?php

namespace App\Models;

use App\Constants\NotificationTemplate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\GameType;
use App\Enums\NotificationAction;
use App\Http\Resources\API\V1\MediaResource;
use App\Notifications\SendPushNotification;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Notification;
use \Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

class Vision extends Model
{
    use HasFactory;

    // protected $casts = [
    //     'title' => 'array',
    // ];

    public function questions()
    {
        return $this->hasMany(VisionQuestion::class);
    }

    public function assignments()
    {
        return $this->hasMany(VisionAssign::class);
    }

    public function questionAnswers()
    {
        return $this->hasMany(VisionQuestionAnswer::class, 'vision_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(LaSubject::class, 'la_subject_id');
    }

    public function laLevel(): BelongsTo
    {
        return $this->belongsTo(LaLevel::class, 'la_level_id');
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

    public function visionAssigns()
    {
        return $this->hasMany(VisionAssign::class, 'vision_id');
    }

    public function userStatus()
    {
        return $this->hasOne(VisionUserStatus::class, 'vision_id')
                    ->where('user_id', auth()->id()); 
    }

    public function sendAssignedVisionNotification($userIds)
    {
        User::whereIn('id', $userIds)
            ->orderBy('id', 'asc')
            ->chunk(50, function ($users) {
                $notification = NotificationTemplate::NEW_VISION_ASSIGNED;

                foreach ($users as $user) {
                    if (!$user->device_token) {
                        continue;
                    }

                    $payload = [
                        'action'        => NotificationAction::Vision(),
                        'vision_id'     => $this->id ?? '',
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

    public function sendApproveNotification(User $toUser)
    {
        $tokens = $toUser->canSendNotification() ? [$toUser->device_token] : [];
        $notification = NotificationTemplate::VISION_APPROVED;
        $vision = $this->fresh();
        $payload = [
            'action' => NotificationAction::Vision(),
            'vision_id' => $vision->id,
            'la_subject_id' => $vision->la_subject_id ?? '',
            'la_level_id' => $vision->la_level_id ?? '',
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
        $notification = NotificationTemplate::VISION_REJECTED;
        $vision = $this->fresh();
        $payload = [
            'action' => NotificationAction::Vision(),
            'vision_id' => $vision->id,
            'la_subject_id' => $vision->la_subject_id ?? '',
            'la_level_id' => $vision->la_level_id ?? '',
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
        return "Vision";
    }

    public function getCoinableObject()
    {
        return [
            'id' => $this->id,
            'title' => $this->default_title ?? ($this->title['en'] ?? 'Untitled Vision')
        ];
    }

    public function userStatuses()
    {
        return $this->hasMany(VisionUserStatus::class, 'vision_id', 'id');
    }

    public function campaigns()
    {
        return $this->hasMany(LaCampaign::class, 'reference_id', 'id')
            ->where('game_type', 7);
    }

    public function getGamePoints($questionType = null)
    {
        $level = $this->laLevel;

        if (!$level) return 0;

        if ($questionType === 'option') {
            return $level->vision_mcq_points ?? 0;
        } elseif (in_array($questionType, ['text', 'image'])) {
            return $level->vision_text_image_points ?? 0;
        }
        return 0;
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class, 'chapter_id');
    }

}
