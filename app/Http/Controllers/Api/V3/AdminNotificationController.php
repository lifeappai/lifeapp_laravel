<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use App\Models\AdminMessage;
use App\Models\User;
use App\Notifications\AdminMessageNotification;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'title' => 'required|string',
            'message' => 'required|string',
        ]);

        // Save message with user_ids in JSON column
        $adminMessage = AdminMessage::create([
            'title' => $request->title,
            'body' => $request->message,
            'user_ids' => $request->user_ids,
        ]);

        $dataPayload = [
            'action' => \App\Enums\NotificationAction::AdminMessage,
            'admin_message_id' => $adminMessage->id,
        ];

        User::whereIn('id', $request->user_ids)
            ->orderBy('id')
            ->chunk(50, function ($users) use ($request, $dataPayload) {
                foreach ($users as $user) {
                    $user->notify(new AdminMessageNotification(
                        $request->title,
                        $request->message,
                        [$user->device_token],
                        $dataPayload
                    ));
                }
            });

        return response()->json([
            'status' => true,
            'message' => 'Notification sent successfully',
            'message_id' => $adminMessage->id,
        ]);
    }

}
