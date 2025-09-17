<?php

namespace App\Http\Controllers\Api\V2;

use App\Constants\NotificationTemplate;
use App\Enums\NotificationAction;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Resources\PublicUserResrouce;
use App\Models\CoinTransaction;
use App\Models\LaQuery;
use App\Models\User;
use App\Notifications\SendPushNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LaQueryController extends ResponseController
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->isMentor()) {
            $query = $user->assignedQueries()
                ->with('createdBy', 'subject');
        } else {
            $query = $user->laQueries()->with('subject', 'mentor');
        }

        if ($request->has('subject_id')) {
            $query->where('la_subject_id', $request->get('subject_id'));
        }

        $queries = $query->latest()->get();

        return $this->sendResponse($queries, 'Queries');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'la_subject_id' => 'required|exists:la_subjects,id',
            'description' => 'required',
            'media' => 'nullable|image|max:5000'
        ]);

        $user = $request->user();

        if ($user->isMentor()) {
            return $this->sendUnauthorizedError();
        }

        $subjectId = $data['la_subject_id'];

        $mentor = User::where('type', UserType::Mentor)->whereHas('mentorSubjects', function ($query) use ($subjectId) {
            return $query->where('la_subject_id', $subjectId);
        })->whereDoesntHave('assignedQueries', function ($query) {
            $query->where('status_id', LaQuery::STATUS_OPEN);
        })->first();

        $data['mentor_id'] = 1;
        if ($mentor) {
            $data['mentor_id'] = $mentor->id;
        }

        $query = $user->laQueries()->create($data);
        $query->load('mentor');

        $notification = NotificationTemplate::QUERY_FOR_MENTOR;

        $pushNotification = new SendPushNotification(
            $notification['title'],
            sprintf($notification['body'], $user->name),
            [$query->mentor->device_token],
            [
                'action' => NotificationAction::Query(),
                'action_id' => $query->id,
                'created_by' => new PublicUserResrouce($user),
            ]
        );

        $query->mentor->notify($pushNotification);

        return $this->sendResponse($query, 'Query posted successfully');
    }

    /**
     * @param Request $request
     * @param LaQuery $laQuery
     * @return \Illuminate\Http\JsonResponse
     */
    public function assign(Request $request, LaQuery $laQuery)
    {
        $user = $request->user();
        if (!$user->isMentor()) {
            return $this->sendUnauthorizedError();
        }
        $laQuery->mentor_id = $request->user()->id;
        $laQuery->save();

        return $this->sendResponse($laQuery, 'query has been assigned');
    }

    /**
     * @param Request $request
     * @param LaQuery $laQuery
     * @return \Illuminate\Http\JsonResponse
     */
    public function reply(Request $request, LaQuery $laQuery)
    {
        $user = $request->user();
        if ($user->id != $laQuery->mentor_id && $user->id != $laQuery->created_by) {
            return $this->sendUnauthorizedError();
        }

        if ($laQuery->mentor_id == null && $user->isMentor()) {
            $laQuery->mentor_id = $user->id;
            $laQuery->save();
        }

        $reply = $laQuery->laReplies()->create([
            'user_id' => $user->id,
            'text' => $request->get('text'),
        ]);

        if ($user->isMentor() && $laQuery->laReplies()->count() == 1) {
            $notification = NotificationTemplate::REPLY_FROM_MENTOR;

            $pushNotification = new SendPushNotification(
                $notification['title'],
                sprintf($notification['body'], $user->name),
                [$laQuery->createdBy->device_token],
                [
                    'action' => NotificationAction::Query(),
                    'action_id' => $laQuery->id,
                    'reply_id' => $reply->id,
                    'created_by' => new PublicUserResrouce($user),
                ]
            );

            $laQuery->createdBy->notify($pushNotification);
            Log::info("mentor first notification");
        } elseif ($user->isMentor()) {
            $pushNotification = new SendPushNotification(
                "Life App",
                $reply->text,
                [$laQuery->createdBy->device_token],
                [
                    'action' => NotificationAction::Query(),
                    'action_id' => $laQuery->id,
                    'reply_id' => $reply->id,
                    'created_by' => new PublicUserResrouce($user),
                ]
            );

            $laQuery->createdBy->notify($pushNotification);
            Log::info("to student reply text notification");
        } else {
            $pushNotification = new SendPushNotification(
                "Life App",
                $reply->text,
                [$laQuery->mentor->device_token],
                [
                    'action' => NotificationAction::Query(),
                    'action_id' => $laQuery->id,
                    'reply_id' => $reply->id,
                    'created_by' => new PublicUserResrouce($user),
                ]
            );

            $laQuery->mentor->notify($pushNotification);

            Log::info("to mentor reply text notification");
        }

        return $this->sendResponse($reply, 'Reply has been posted.');
    }

    /**
     * @param Request $request
     * @param LaQuery $laQuery
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReplies(Request $request, LaQuery $laQuery)
    {
        $user = $request->user();

        if ($user->id != $laQuery->mentor_id && $user->id != $laQuery->created_by) {
            return $this->sendUnauthorizedError();
        }

        $replies = $laQuery->laReplies();

        if ($request->has('after')) {
            $replies->where('id', '>', $request->get('after'));
        }

        $replies = $replies->get();

        return response()->json([
            'data' => $replies,
            'query_status' => $laQuery->status
        ]);
    }

    /**
     * @param Request $request
     * @param LaQuery $laQuery
     * @return \Illuminate\Http\JsonResponse
     */
    public function closeQuery(Request $request, LaQuery $laQuery)
    {
        $user = $request->user();
        if ($user->id == $laQuery->mentor_id) {
            $laQuery->status_id = LaQuery::STATUS_CLOSED;
            $laQuery->save();

            $notification = NotificationTemplate::CLOSE_QUERY;

            $pushNotification = new SendPushNotification(
                $notification['title'],
                sprintf($notification['body'], $user->name),
                [$laQuery->createdBy->device_token],
                [
                    'action' => NotificationAction::Query(),
                    'action_id' => $laQuery->id,
                    'created_by' => new PublicUserResrouce($user),
                ]
            );

            $laQuery->createdBy->notify($pushNotification);

            return $this->sendResponse($laQuery, 'Replies');
        }
        return $this->sendUnauthorizedError();
    }

    /**
     * @param Request $request
     * @param LaQuery $laQuery
     * @return \Illuminate\Http\JsonResponse
     */
    public function feedbackQuery(Request $request, LaQuery $laQuery)
    {
        $user = $request->user();
        if (!$user->isStudent() || !$laQuery->isClosed()) {
            return $this->sendUnauthorizedError();
        }

        $request->validate([
            'rating' => 'required|min:1|max:5',
            'feedback' => 'string|nullable',
        ]);

        $laQuery->rating = $request->get('rating');
        $laQuery->feedback = $request->get('feedback');
        $laQuery->save();

        $coins = $laQuery->coinsForMentor();

        $laQuery->mentor->createTransaction($laQuery, $coins, CoinTransaction::TYPE_QUERY);

        $notification = NotificationTemplate::QUERY_FEEDBACK;

        $pushNotification = new SendPushNotification(
            $notification['title'],
            sprintf($notification['body'], $user->name, "{$laQuery->rating}"),
            [$laQuery->createdBy->device_token],
            [
                'action' => NotificationAction::Query(),
                'action_id' => $laQuery->id,
                'created_by' => new PublicUserResrouce($user),
            ]
        );

        $laQuery->createdBy->notify($pushNotification);

        return $this->sendResponse($laQuery, 'feedback has been posted.');
    }
}
