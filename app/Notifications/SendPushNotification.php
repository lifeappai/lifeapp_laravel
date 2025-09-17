<?php

namespace App\Notifications;

use App\Enums\NotificationAction;
use App\Models\PushNotificationCampaign;
use App\Helpers\FirebaseService; // ✅ Use your service
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use App\Channels\FirebaseChannel;

class SendPushNotification extends Notification
{
    use Queueable;

    protected $title;
    protected $message;
    protected $fcmTokens;
    protected $data = [];

    public function __construct(string $title, string $message, array $fcmTokens, $data = [])
    {
        $this->title = $title;
        $this->message = $message;
        $this->fcmTokens = $fcmTokens;
        $this->data = $data;
    }

    public function toArray($notifiable)
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'data' => $this->data,
            'channelId' => 'lifelab',
        ];
    }

    public function via($notifiable)
    {
        return [FirebaseChannel::class, 'database'];
    }

    public function pushNotificationCampaign()
    {
        $action = $this->data['action'] ?? -1;
        if ($action == NotificationAction::AdminCampaign()) {
            return PushNotificationCampaign::find($this->data['action_id']);
        }
        return null;
    }

    public function toFirebase($notifiable)
    {
        return [
            'tokens' => $this->fcmTokens,
            'title'  => $this->title,
            'body'   => $this->message,
            'data'   => $this->toArray($notifiable), // 👈 includes extra payload
        ];
    }

}