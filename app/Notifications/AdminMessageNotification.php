<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminMessageNotification extends Notification
{
    use Queueable;

    protected string $title;
    protected string $message;
    protected array $data;

    public function __construct(string $title, string $message, array $data = [])
    {
        $this->title   = $title;
        $this->message = $message;
        $this->data    = $data;
    }

    public function via($notifiable)
    {
        return ['database']; // ✅ only DB
    }

    public function toArray($notifiable)
    {
        return [
            'title'     => $this->title,
            'message'   => $this->message,
            'data'      => $this->data,
            'channelId' => 'lifelab',
        ];
    }
}
