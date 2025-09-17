<?php

namespace App\Jobs;

use App\Constants\NotificationTemplate;
use App\Enums\NotificationAction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Notifications\SendPushNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendSessionNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $laSessionId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($laSessionId)
    {
        $this->laSessionId = $laSessionId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            Log::info("Send Session notification Start for la_session_id: $this->laSessionId");
            $chunkSize = 500;
            $totalUsersProcessed = 0;
            // User::orderBy('id', 'asc')->chunk($chunkSize, function ($users) use (&$totalUsersProcessed) {
            User::orderBy('id', 'asc')->whereIn('mobile_no', ['9561324753', '8554051128', '8712144839', '6353387333', '8600000007', '8600000008', '8600000009', '8600000010', '8600000012', '9561324753'])->chunk($chunkSize, function ($users) use (&$totalUsersProcessed) {
                $totalUsersProcessed += $users->count();
                Log::info("Processing chunk. users fetch: $totalUsersProcessed");

                $tokens = $users->whereNotNull('device_token')->pluck('device_token')->toArray();
                $notification = NotificationTemplate::NEW_SESSION;

                $payload = [
                    'action' => NotificationAction::Session(),
                    'la_session_id' => $this->laSessionId ?? '',
                ];

                $pushNotification = new SendPushNotification(
                    $notification['title'],
                    $notification['body'],
                    $tokens,
                    $payload
                );
                Notification::send($users, $pushNotification);
            });
            Log::info("All chunks processed successfully. Total users fetched: $totalUsersProcessed");
            Log::info("Send Session notification end");
            return true;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return ;
        }
    }
}