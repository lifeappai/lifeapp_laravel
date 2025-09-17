<?php

namespace App\Http\Controllers\Admin;

use App\Constants\NotificationTemplate;
use App\Enums\NotificationAction;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Models\LaQuery;
use App\Models\LaQueryReply;
use App\Models\User;
use App\Notifications\SendPushNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

class LaQueriesController extends Controller
{
    public function index(Request $request)
    {
        $queries = LaQuery::query();
        if ($request->status != null) {
            $queries->where('status_id', $request->status);
        } else {
            $queries->where('waiting_reply', 1)
                ->where('status_id', LaQuery::STATUS_OPEN);
        }

        if ($request->mentor_id) {
            $queries = $queries->where('mentor_id', $request->mentor_id);
        }
        $mentors = User::where('type', UserType::Mentor)->get(['id', 'name']);
        $queries = $queries->paginate(25);
        return view('pages.admin.queries.index', compact('queries', 'request', 'mentors'));
    }

    public function viewChats(LaQuery $laQuery, Request $request)
    {
        $loginUser = Auth::user()->id;
        $queryId = $laQuery->id;
        $laReplies = LaQueryReply::where('la_query_id', $laQuery->id)->get();
        $creactedBy = $laQuery->created_by;
        if ($request->ajax()) {
            $totalReplies = count($laReplies);
            $html = view('pages.admin.queries.query-replies-html', compact('laReplies', 'creactedBy', 'queryId', 'loginUser', 'laQuery'))->render();
            return response()->json(['html' => $html]);
        }
        return view('pages.admin.queries.query-replies', compact('laReplies', 'creactedBy', 'queryId', 'loginUser', 'laQuery'));
    }

    public function reply(LaQuery $laQuery, Request $request)
    {
        $userName = Auth::user()->name;
        $reply = $laQuery->laReplies()->create([
            'user_id' => $request->userId,
            'text' => $request->message,
        ]);
        $user = User::where('id', $request->userId)->first();
        $tokens = User::where('id', $request->userId)->whereNotNull('device_token')->pluck('device_token')->toArray();
        $notification = NotificationTemplate::ADMIN_QUERY_MESSAGE;
        $payload = [
            'action' => NotificationAction::Mission(),
            'action_id' => Auth::user()->id,
        ];

        $pushNotification = new SendPushNotification(
            $notification['title'],
            sprintf($notification['body'], "{$userName}"),
            $tokens,
            $payload
        );
        Notification::send($user, $pushNotification);
        return response()->json(['success' => 1]);
    }

    public function changeStatus(LaQuery $laQuery)
    {
        if ($laQuery->status_id == LaQuery::STATUS_OPEN) {
            $laQuery->update(['status_id' => LaQuery::STATUS_CLOSED]);
            $notification = NotificationTemplate::ADMIN_CLOSE_QUERY;
        } else {
            $laQuery->update(['status_id' => LaQuery::STATUS_OPEN]);
            $notification = NotificationTemplate::ADMIN_OPEN_QUERY;
        }
        $pushNotification = new SendPushNotification(
            $notification['title'],
            sprintf($notification['body'], Auth::user()->name),
            [$laQuery->createdBy->device_token],
            [
                'action' => NotificationAction::Query(),
                'action_id' => $laQuery->id,
            ]
        );
        $laQuery->createdBy->notify($pushNotification);

        return response()->json(['msg' => 'Status changed successfully']);
    }
}
