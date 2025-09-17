<?php

namespace App\Http\Controllers\Api\V1;

use App\Constants\NotificationTemplate;
use App\Enums\NotificationAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\CampaignUserResource;
use App\Http\Resources\CampaignResource;
use App\Http\Resources\CouponResource;
use App\Http\Resources\RequestPeopleResource;
use App\Models\Campaign;
use App\Models\CoinTransaction;
use App\Notifications\SendPushNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class CampaignController extends Controller
{
	/**
	 * @return JsonResponse
	 */
	public function getCampaigns(): JsonResponse
	{
	    $campaigns = Campaign::where(['user_id' => Auth::id()])->latest()->get();
		return new JsonResponse([
		    'campaigns' => CampaignResource::collection($campaigns)
        ], Response::HTTP_OK);
	}

	/**
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function createCampaign(Request $request): JsonResponse
	{
		$data = $request->validate(
			[
				"coupon_id" => ['required', 'integer'],
				'title' => ['required', 'string'],
				'reason' => ['required', 'string'],
				'coin' => ['required', 'integer'],
				'friend_request' => ['required', 'array'],
			]
		);

		$data['user_id'] = Auth::id();
		Campaign::create($data);
		return new JsonResponse([
				'message' => "Campaign Create successfully",
			],
			Response::HTTP_OK
		);
	}

	public function delete(Campaign $campaign)
    {
        $campaign->delete();
        return new JsonResponse([
            'message' => "Campaign has been deleted successfully",
        ],
            Response::HTTP_OK
        );

    }

	public function requestingPeople(Request $request)
    {
        return new JsonResponse(
            (
                [
                    'request_people_list' => RequestPeopleResource::collection(Campaign::whereJsonContains('friend_request', "" . Auth::id())->get())
                ]
            )
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            "coupon_id" => ['integer'],
            'title' => ['required', 'string'],
            'reason' => ['required', 'string'],
            'coins' => ['required', 'integer'],
            'users' => ['required', 'array'],
            'users.*' => ['required', 'exists:users,id']
        ]);
        $user = $request->user();

        $data['user_id'] = $user->id;
        $data['friend_request'] = $data['users'];
        $campaign = Campaign::create($data);
        $campaign->users()->attach($data['users']);
        $campaign->load('users');


        $tokens = $campaign->users->whereNotNull('device_token')->pluck('device_token')->toArray();

        $notification = NotificationTemplate::CAMPAIGN_INVITATION;
        $payload = [
            'action' => NotificationAction::Campaign(),
            'action_id' => $campaign->id,
            'media_url' => $user->image_path ?? null,
        ];

        $pushNotification = new SendPushNotification(
            $notification['title'],
            sprintf($notification['body'], $user->name),
            $tokens,
            $payload
        );

        Notification::send($campaign->users, $pushNotification);

        return new JsonResponse([
            'campaign' => new \App\Http\Resources\API\V1\CampaignResource($campaign),
        ], Response::HTTP_OK);
    }

    public function update(Request $request, Campaign $campaign)
    {
        $request->validate([
            'users' => ['nullable', 'array'],
            'users.*' => ['nullable', 'exists:users,id']
        ]);

        $campaign->update($request->all());
        if ($request->has('users')) {
            $campaign->users()->sync($request->get('users'));
        }

        return new JsonResponse([
            'campaign' => new \App\Http\Resources\API\V1\CampaignResource($campaign),
        ], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $campaigns = $user->campaigns()->with(['users' => function($users) {
            $users->whereNotNull('given_at');
        }])->latest()->get();

        return new JsonResponse([
            'campaigns' => \App\Http\Resources\API\V1\CampaignResource::collection($campaigns),
        ], Response::HTTP_OK);
    }

    /**
     * @param $campaign
     * @return JsonResponse
     */
    public function show(Campaign $campaign)
    {
        $users = $campaign->users()->withPivot('coins', 'given_at')->get();
        return new JsonResponse([
            'campaign' => [
                'id' => $campaign->id,
                'title' => $campaign->title,
                'reason' => $campaign->reason,
                'coins' => $campaign->coins,
                'received_coins' => $campaign->givenCoins()->sum('coins'),
                'coupon' => $campaign->coupon ? new CouponResource($campaign->coupon) : null,
                'users' => CampaignUserResource::collection($users),
                'given_users' => CampaignUserResource::collection($users->whereNotNull('pivot.coins')),
                'completed_at' => $campaign->completed_at,
                'created_at' => $campaign->created_at,
                'updated_at' => $campaign->updated_at,
            ]
        ], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function requestingCampaigns(Request $request)
    {
        $user = $request->user();
        $campaigns = $user->requestingCampaigns()->pending()->with('createdBy')->get();

        return new JsonResponse([
            'campaigns' => \App\Http\Resources\API\V1\CampaignResource::collection($campaigns),
        ], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param Campaign $campaign
     * @return JsonResponse
     */
    public function coinsGiven(Request $request, Campaign $campaign)
    {
        $user = $request->user();

        $data = $request->validate([
            'brain_coins' => ['integer', 'max:' . $user->brain_coins],
            'heart_coins' => ['integer' , 'max:' . $user->heart_coins]
        ]);

        $campaignAssignment = $campaign->givenCoins()->where('user_id', $request->user()->id)->first();

        $createdBy = $campaign->createdBy()->first();

        if ($campaignAssignment) {
            DB::beginTransaction();
            if (isset($data['brain_coins'])) {
                $campaignAssignment->setCoins($data['brain_coins']);
                $user->createTransaction($campaignAssignment, -1*$data['brain_coins'], CoinTransaction::TYPE_BRAIN);
                $createdBy->createTransaction($campaignAssignment, $data['brain_coins'], CoinTransaction::TYPE_BRAIN);

            } elseif (isset($data['heart_coins'])) {
                $campaignAssignment->setCoins($data['heart_coins']);
                $user->createTransaction($campaignAssignment, -1*$data['heart_coins'], CoinTransaction::TYPE_HEART);
                $createdBy->createTransaction($campaignAssignment, $data['heart_coins'], CoinTransaction::TYPE_HEART);
            }
            $givenCoins = $campaign->givenCoins()->sum('coins');

            if ($campaign->coins <= $givenCoins) {
                $campaign->completed();
            }

            DB::commit();

            $tokens = $createdBy->canSendNotification() ? [$createdBy->device_token] : [];
            $notification = NotificationTemplate::CAMPAIGN_INVITATION_SUPPORT;

            $payload = [
                'action' => NotificationAction::Campaign(),
                'action_id' => $campaign->id,
                'user_id' => $user->id,
                'media_url' => $user->image_path ?? null,
            ];

            $pushNotification = new SendPushNotification(
                $notification['title'],
                sprintf($notification['body'], $user->name, $campaignAssignment->coins),
                $tokens,
                $payload
            );

            $createdBy->notify($pushNotification);

            return new JsonResponse([
                'message' => 'Coins has been transferred successfully.'
            ], Response::HTTP_OK);
        }

        return new JsonResponse([
            'error' => 'Invalid campaign'
        ], Response::HTTP_BAD_REQUEST);
    }
}
