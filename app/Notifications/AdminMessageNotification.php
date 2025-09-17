<?php

namespace App\Notifications;

use App\Enums\NotificationAction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Facades\Larafirebase;

class AdminMessageNotification extends Notification
{
    use Queueable;

    protected string $title;
    protected string $message;
    protected array $tokens;
    protected array $data;

    public function __construct(string $title, string $message, array $tokens, array $data = [])
    {
        $this->title = $title;
        $this->message = $message;
        $this->tokens = $tokens;
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['firebase', 'database']; // Optional: Add 'mail' or 'broadcast' if needed
    }

    public function toFirebase($notifiable)
    {
        if (empty($this->tokens)) {
            return;
        }

        return Larafirebase::withTitle($this->title)
            ->withBody($this->message)
            ->withAdditionalData($this->data)
            ->sendNotification($this->tokens);
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
}
    