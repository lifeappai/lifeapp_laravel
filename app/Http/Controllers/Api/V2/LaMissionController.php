<?php

namespace App\Http\Controllers\Api\V2;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\V2\LaMissionCompleteResource;
use App\Http\Resources\API\V2\LaMissionResource;
use App\Models\LaMission;
use App\Models\LaMissionComplete;
use App\Models\LaMissionUserTiming;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LaMissionController extends ResponseController
{
    public function index(Request $request)
    {
        try {
            $validate = array(
                'la_subject_id' => ['required', 'exists:la_subjects,id'],
            );
            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }
            $missions = LaMission::where('status', StatusEnum::ACTIVE)
                ->where('la_subject_id', $request->la_subject_id);

            if ($request->search_lang && $request->search_title) {
                $missions = $missions->where(function ($query) use ($request) {
                    $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$." . $request->search_lang . "')) LIKE ?", ["%" . $request->search_title . "%"]);
                });
            }

            $missions = $missions->withCount(['missionCompletes' => function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            }])
                ->orderByRaw('CASE WHEN mission_completes_count = 0 THEN 0 ELSE 1 END ASC')
                ->orderBy('index')
                ->paginate(15);
            $response['missions'] =  LaMissionResource::collection($missions)->response()->getData(true);
            return $this->sendResponse($response, "Missions");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function updateMissionUserTiming(Request $request)
    {
        try {
            $validate = array(
                'la_mission_resource_id' => ['required', 'exists:la_mission_resources,id'],
                'timing' => ['required'],
            );
            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }

            $missionComplete = LaMissionUserTiming::where('user_id', Auth::id())->where('la_mission_resource_id', $request->la_mission_resource_id)->latest()->first();
            if ($missionComplete) {
                return $this->sendError("Mission already submitted");
            }

            LaMissionUserTiming::create([
                "user_id" => Auth::id(),
                "la_mission_resource_id" => $request->la_mission_resource_id,
                "timings" => $request->timing,
            ]);

            return $this->sendResponse("", "Mission timing added successfully");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function completeMission(Request $request)
    {
        try {
            $validate = array(
                'la_mission_id' => ['required', 'exists:la_missions,id'],
                'media' => ['required'],
                'description' => ['required'],
            );
            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }

            $missionComplete = LaMissionComplete::where('user_id', Auth::id())->where('la_mission_id', $request->la_mission_id)->latest()->first();
            if ($missionComplete && ($missionComplete->isInReview() || $missionComplete->isApproved())) {
                return response()->json([
                    'error' => 'Mission already submitted.'
                ], 400);
            }

            $mediaId = null;
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
                $mediaId = $missionResourceMedia->id;
            }

            LaMissionComplete::create([
                "user_id" => Auth::id(),
                "la_mission_id" => $request->la_mission_id,
                "media_id" => $mediaId,
                "description" => $request->description,
            ]);
            return $this->sendResponse("", "Mission Complete successfully");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function show(LaMission $laMission)
    {
        try {
            $response = new LaMissionResource($laMission);
            return $this->sendResponse($response, "Mission Details");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function userMissionSubmissions(Request $request)
    {
        try {
            $validate = array(
                'user_id' => ['required', 'exists:users,id'],
            );
            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }

            $submittedMissions = LaMissionComplete::orderBy('created_at', 'desc')->where('user_id', $request->user_id)->get();

            $users = LaMissionCompleteResource::collection($submittedMissions);
            return $this->sendResponse($users, "Users");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
