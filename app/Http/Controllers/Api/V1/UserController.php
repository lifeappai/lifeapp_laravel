<?php

namespace App\Http\Controllers\Api\V1;

use App\Constants\NotificationTemplate;
use App\Enums\NotificationAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\FriendRequestResource;
use App\Http\Resources\API\V1\UserWithFriendsResource;
use App\Http\Resources\PublicUserResrouce;
use App\Http\Resources\API\V1\UserResource;
use App\Models\Friendship;
use App\Models\User;
use App\Notifications\SendPushNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;


class UserController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function usersList(Request $request): JsonResponse
    {
        $users = User::where(['mobile_no' => $request->mobile_no])->get();

        return new JsonResponse([
            'verify' => true,
            'users' => PublicUserResrouce::collection($users)
        ], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id' => 'required|integer',
            'pin' => 'required'
        ]);

        $user = User::where('id', $data['id'])->first();
        if ($user && Hash::check($data['pin'], $user->password)) {
            $token = $user->createToken('LifeApp')->accessToken;
            return new JsonResponse(['token' => $token], Response::HTTP_OK);
        }

        return response()->json([
            'error' => "Invalid Pin"
        ], 401);
    }

    public function createPin(Request $request)
    {
        $data = $request->validate([
            'pin' => 'required|min:4'
        ]);

        $users = User::where(['mobile_no' => $request->mobile_no])->count();
        throw_if($users >= 3, new \ErrorException('Number already exist', 422));

        $user = new User([
            'mobile_no' => $request->mobile_no,
        ]);
        $user->password = Hash::make($data['pin']);
        $user->save();

        $token = $user->createToken('LifeApp')->accessToken;
        return new JsonResponse(['token' => $token], Response::HTTP_OK);
    }


    /**
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        return new JsonResponse([
            'user' => new UserResource(Auth::User())
        ], Response::HTTP_OK);
    }

    /**
     * @return JsonResponse
     */
    public function logout()
    {
        Auth::user()->token()->revoke();
        return new JsonResponse(['message' => "Logout Successfully"], Response::HTTP_OK);
    }

    /**
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $query = User::query();
        $search = $request->get('search');
        if (!empty($search)) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        return new JsonResponse([
            'users' => UserWithFriendsResource::collection(
                $query->get()
            )
        ], Response::HTTP_OK);
    }

    public function getFriendRequest(Request $request)
    {
        $user = $request->user();

        $friendList = $user->myFriendRequests()->withPivot('id', 'status', 'created_at')->get();

        return new JsonResponse(['friend-requests' => FriendRequestResource::collection($friendList)], Response::HTTP_OK);
    }

    /**
     * @return JsonResponse
     */
    public function sendFriendRequest(Request $request)
    {
        $data = $request->validate([
            "sender_id" => ['required', 'integer'],
            'recipient_id' => ['required', 'integer'],
            'status' => ['string'],
        ]);

        $friendship = Friendship::updateOrCreate([
            'sender_id' => $data['sender_id'],
            'recipient_id' => $data['recipient_id'],
        ], [
            'status' => 'pending',
        ]);

        $sender = $request->user();

        $recipient = User::find($data['recipient_id']);
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

        return new JsonResponse(['message' => 'Friend request sent successfully'], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param Friendship $friendship
     * @return JsonResponse
     */
    public function confirmFriendRequest(Request $request, Friendship $friendship)
    {
        $user = $request->user();
        if ($user->id != $friendship->recipient_id) {
            return new JsonResponse(['error' => "You can't perform this action."], Response::HTTP_UNAUTHORIZED);
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

        return new JsonResponse(['message' => 'Friend request confirmed successfully'], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param Friendship $friendship
     * @return JsonResponse
     */
    public function rejectFriendRequest(Request $request, Friendship $friendship)
    {
        $user = $request->user();
        if ($user->id != $friendship->recipient_id) {
            return new JsonResponse(['error' => "You can't perform this action."], Response::HTTP_UNAUTHORIZED);
        }

        $friendship->delete();

        return new JsonResponse(['message' => 'Friend request rejected.'], Response::HTTP_OK);
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function friendList(User $user)
    {
        $q1 = DB::table('friendships')->select(DB::raw('recipient_id as user_id'))
            ->where('sender_id', $user->id)
            ->where('status', 'confirmed');

        $q2 = DB::table('friendships')->select(DB::raw('sender_id as user_id'))
            ->where('recipient_id', $user->id)
            ->where('status', 'confirmed');

        $userIds = $q1->union($q2)->pluck('user_id')->toArray();

        return new JsonResponse([
            'friend-list' => PublicUserResrouce::collection(User::whereIn('id', $userIds)->get())
        ], Response::HTTP_OK);
    }

    public function deleteFriend(Request $request, User $user)
    {

        DB::table('friendships')
            ->where('sender_id', $request->user()->id)
            ->where('recipient_id', $user->id)
            ->where('status', 'confirmed')
            ->delete();

        DB::table('friendships')
            ->where('sender_id', $user->id)
            ->where('recipient_id', $request->user()->id)
            ->where('status', 'confirmed')
            ->delete();

        return new JsonResponse([
            'message' => 'friend is removed.'
        ], Response::HTTP_OK);
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function show(User $user)
    {
        return new JsonResponse(['user' => new UserResource(Auth::User())], Response::HTTP_OK);
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function friendRequests(User $user)
    {
        $friendList = $user->friendRequests()->withPivot('id', 'status', 'created_at')->get();

        return new JsonResponse(['friend-requests' => FriendRequestResource::collection($friendList)], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateDeviceToken(Request $request)
    {
        $data = $request->validate([
            'device' => 'required|in:ios,android',
            'device_token' => 'required'
        ]);

        $user = $request->user();
        $user->update($data);

        return new JsonResponse([
            'message' => 'Device token is updated.',
        ], Response::HTTP_OK);
    }

    public function getNotifications(Request $request)
    {
        $notifications = $request->user()->notifications;
        return new JsonResponse([
            'notification' => $notifications,
        ], Response::HTTP_OK);

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteNotifications(Request $request)
    {
        $request->user()->notifications()->delete();
        return new JsonResponse([
            'message' => "Notification has bean cleared."
        ], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function readNotifications(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return new JsonResponse([
            'message' => "Notification has bean read."
        ], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getCoinTransactions(Request $request)
    {
        $user = $request->user();

        return $user->coinTransactions()->has('coinable')->with('coinable')->latest()->simplePaginate();
    }
}
