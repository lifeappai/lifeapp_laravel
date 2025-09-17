<?php

namespace App\Http\Controllers\Api\V3;

use App\Constants\NotificationTemplate;
use App\Enums\NotificationAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\FriendRequestResource;
use App\Http\Resources\PublicUserResrouce;
use App\Models\Friendship;
use App\Models\User;
use App\Notifications\SendPushNotification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FriendController extends ResponseController
{
    public function getFriends(Request $request)
    {
        try {
            $user = Auth::user();
            $q1 = Friendship::select(DB::raw('recipient_id as user_id'))
                ->where('sender_id', $user->id)
                ->where('deleted_at', null)
                ->where('status', 'confirmed');

            $q2 = Friendship::select(DB::raw('sender_id as user_id'))
                ->where('recipient_id', $user->id)
                ->where('deleted_at', null)
                ->where('status', 'confirmed');

            $userIds = $q1->union($q2)->pluck('user_id')->toArray();

            $friends = User::whereIn('id', $userIds)->with('school');
            $search = $request->get('search');
            if (!empty($search)) {
                $friends->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('mobile_no', 'like', '%' . $search . '%');
                });
            }
            $friends = $friends->get();

            $friends = PublicUserResrouce::collection($friends);

            $response['friends_count'] = $friends->count();
            $response['friends'] = $friends;
            return $this->sendResponse($response, "Friends");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function friendRequests()
    {
        try {
            $user = User::find(Auth::user()->id);

            $friendList = $user->friendRequests()->with('school')
                ->withPivot('id', 'status', 'created_at')
                ->get();

            $friends = PublicUserResrouce::collection(FriendRequestResource::collection($friendList));
            return $this->sendResponse($friends, "Friends");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function getInviteRequests()
    {
        try {
            $user = User::find(Auth::user()->id);
            $friendList = $user->myFriendRequests()->withPivot('id', 'status', 'created_at')->get();
            $friends = PublicUserResrouce::collection(FriendRequestResource::collection($friendList));
            return $this->sendResponse($friends, "Friends");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function sendFriendRequest(Request $request)
    {
        try {
            $validate = array(
                "sender_id" => ['required', 'exists:users,id'],
                'recipient_id' => ['required', 'exists:users,id'],
            );
            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }

            $friendship = Friendship::updateOrCreate([
                'sender_id' => $request->sender_id,
                'recipient_id' => $request->recipient_id,
            ], [
                'status' => 'pending',
            ]);

            $sender = $request->user();

            $recipient = User::find($request->recipient_id);
            $tokens = $recipient->canSendNotification() ? [$recipient->device_token] : [];
            $notification = NotificationTemplate::FRIEND_REQUEST;
            $payload = [
                'action' => NotificationAction::Friendship(),
                'action_id' => intval($friendship->sender_id),
                'media_url' => $sender->image_path ?? null,
            ];

            $pushNotification = new SendPushNotification(
                $notification['title'],
                $notification['body'],
                $tokens,
                $payload
            );
            $recipient->notify($pushNotification);
            return $this->sendResponse("", "Friend request sent");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function acceptFriendRequest(Friendship $friendship)
    {
        try {
            $user = Auth::user();
            if ($user->id != $friendship->recipient_id) {
                return response()->json([
                    'error' => "You can't perform this action.",
                ], Response::HTTP_UNAUTHORIZED);
            }

            $friendship->update(['status' => 'confirmed']);
            $friend = $friendship->sender;
            $tokens = $friend->canSendNotification() ? [$friend->device_token] : [];
            $notification = NotificationTemplate::FRIEND_REQUEST_APPROVE;

            $payload = [
                'action' => NotificationAction::Friendship(),
                'action_id' => intval($friendship->recipient_id),
                'media_url' => $friend->image_path ?? null,
            ];

            $pushNotification = new SendPushNotification(
                $notification['title'],
                sprintf($notification['body'], $user->name),
                $tokens,
                $payload
            );
            $friend->notify($pushNotification);
            return $this->sendResponse("", "Friend request accepted");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function rejectFriendRequest(Friendship $friendship)
    {
        try {
            $user = Auth::user();
            if ($user->id != $friendship->recipient_id) {
                return response()->json([
                    'error' => "You can't perform this action.",
                ], Response::HTTP_UNAUTHORIZED);
            }

            $friendship->update(['status' => 'blocked']);
            $friendship->delete();

            return $this->sendResponse("", "Friend request rejected");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function deleteFriend(Request $request, User $user)
    {
        try {
            Friendship::where('sender_id', $request->user()->id)
                ->where('recipient_id', $user->id)
                ->where('status', 'confirmed')
                ->delete();

            Friendship::where('sender_id', $user->id)
                ->where('recipient_id', $request->user()->id)
                ->where('status', 'confirmed')
                ->delete();
            return $this->sendResponse("", "Friend is removed");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
