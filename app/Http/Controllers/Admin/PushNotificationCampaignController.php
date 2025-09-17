<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusEnum;
use App\Http\Controllers\Api\V1\LocationController;
use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\PushNotificationCampaign;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PushNotificationCampaignController extends LocationController
{
    public function index()
    {
        $campaigns = PushNotificationCampaign::orderBy('id', 'desc')->paginate(50);
        return view('pages.admin.push-notification-campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        $states = $this->states("India");
        $states = $states->getData();
        $schools = School::orderBy('name')->where('app_visible', StatusEnum::ACTIVE)->get();
        $countLists = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        return view('pages.admin.push-notification-campaigns.create', compact('states', 'schools', 'countLists'));
    }

    public function store(Request $request)
    {
        $scheduledDate = ($request->scheduled_date && $request->schedule_type == "send_later") ? date("Y-m-d", strtotime($request->scheduled_date)) : null;
        $scheduledTime = ($request->scheduled_time && $request->schedule_type == "send_later") ? date("H:i:s", strtotime($request->scheduled_time)) : null;
        $request->validate([
            'name' => 'required',
            'title' => 'required',
            'body' => 'required',

            'media' => 'nullable|image',
            'schedule_date' => 'nullable|date',
            'school_id' => 'nullable|exists:schools,id'
        ]);

        $campaign = new PushNotificationCampaign($request->all());

        if ($request->media) {
            $missionCompleteImg = $request->media;
            $qMediaName = $missionCompleteImg->getClientOriginalName();
            $qMediaPath = Storage::put('media', $missionCompleteImg);
            $missionResourceMedia = Media::create(
                [
                    'name'    => $qMediaName,
                    'path'    => $qMediaPath
                ]
            );
            $campaign->media_id = $missionResourceMedia->id;
        }

        if ($scheduledDate) {
            $campaign->scheduled_date = $scheduledDate . " " . $scheduledTime;
        }

        $campaign->setUsers($request->all());
        $campaign->created_by = Auth::user()->id;
        $campaign->save();

        if (!$campaign->scheduled_date) {
            $campaign->schedule();
        }

        return redirect()->back()->with('success', 'Campaign Added');
    }
}
