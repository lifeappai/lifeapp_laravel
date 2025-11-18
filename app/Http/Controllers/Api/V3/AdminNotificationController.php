<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use App\Models\AdminMessage;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\AdminMessageNotification;
use App\Notifications\SendPushNotification;

class AdminNotificationController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'title'    => 'required|string',
            'message'  => 'required|string',
        ]);

        $adminMessage = AdminMessage::create([
            'title'    => $request->title,
            'body'     => $request->message,
            'user_ids' => $request->user_ids,
        ]);

        $dataPayload = [
            'action'            => \App\Enums\NotificationAction::AdminMessage,
            'admin_message_id'  => $adminMessage->id,
        ];

        User::whereIn('id', $request->user_ids)
            ->orderBy('id')
            ->chunk(50, function ($users) use ($dataPayload, $request) {
                // ✅ Collect all tokens in this chunk
                $tokens = $users->whereNotNull('device_token')->pluck('device_token')->toArray();

                if (!empty($tokens)) {
                    // ✅ One push call per chunk
                    $pushNotification = new SendPushNotification(
                        $request->title,
                        $request->message,
                        $tokens, // 👈 multiple tokens together
                        $dataPayload
                    );

                    // You don’t need to loop per token, one broadcast
                    \Notification::route('firebase', $tokens)->notify($pushNotification);
                }

                // ✅ Save DB notification per user
                foreach ($users as $user) {
                    $user->notify(new AdminMessageNotification(
                        $request->title,
                        $request->message,
                        $dataPayload
                    ));
                }
            });

        return response()->json([
            'status'     => true,
            'message'    => 'Notification sent successfully',
            'message_id' => $adminMessage->id,
        ]);
    }
}
