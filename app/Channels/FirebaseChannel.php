<?php

namespace App\Channels;

use App\Helpers\FirebaseService;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class FirebaseChannel
{
    protected $fcm;

    public function __construct(FirebaseService $fcm)
    {
        $this->fcm = $fcm;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        if (! method_exists($notification, 'toFirebase')) {
            return;
        }

        $data = $notification->toFirebase($notifiable);

        try {
            if (!empty($data['tokens'])) {
                foreach ($data['tokens'] as $token) {
                    $this->fcm->sendNotification(
                        $token,
                        $data['title'] ?? 'Notification',
                        $data['body'] ?? '',
                        $data['data'] ?? []
                    );
                }
            }
        } catch (\Throwable $e) {
            Log::error('FirebaseChannel failed: ' . $e->getMessage());
        }
    }
}
