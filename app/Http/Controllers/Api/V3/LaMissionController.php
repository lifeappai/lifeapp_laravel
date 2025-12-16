<?php

namespace App\Http\Controllers\Api\V3;

use App\Enums\GameType;
use App\Enums\StatusEnum;
use App\Enums\UserType;
use App\Http\Resources\API\V3\LaMissionCompleteResource;
use App\Http\Resources\API\V3\LaMissionResource;
use App\Models\LaMission;
use App\Models\LaMissionComplete;
use App\Models\LaMissionUserTiming;
use App\Models\Media;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LaMissionController extends ResponseController
{
    public function index(Request $request)
    {
        try {
            $validate = [
                'la_subject_id' => ['nullable', 'exists:la_subjects,id'],
                'la_level_id'   => ['nullable', 'exists:la_levels,id'],
                'chapter_id'    => ['nullable', 'exists:chapters,id'], // ✅ NEW
                'type'          => ['required', Rule::in([GameType::MISSION, GameType::JIGYASA, GameType::PRAGYA])],
                'search_lang'   => ['nullable'],
                'search_title'  => ['nullable'],
            ];

            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }

            $missions = LaMission::where('status', StatusEnum::ACTIVE)
                ->where('type', $request->type);

            if ($request->filled('la_level_id')) {
                $missions->where('la_level_id', $request->la_level_id);
            }

            if ($request->filled('la_subject_id')) {
                $missions->where('la_subject_id', $request->la_subject_id);
            }

            if ($request->filled('chapter_id')) {
                $missions->where('chapter_id', $request->chapter_id); // ✅ NEW
            }

            // Role-based access
            if (Auth::user()->type == UserType::Teacher) {
                // Teachers: 'for all' (1) + 'for teacher' (2)
                $missions->whereIn('allow_for', [GameType::ALLOW_FOR['ALL'], GameType::ALLOW_FOR['BY_TEACHER']]);
            } elseif (Auth::user()->type == UserType::Student) {
                $missions->where(function ($q) {
                    $q->whereIn('allow_for', [GameType::ALLOW_FOR['ALL'], GameType::ALLOW_FOR['BY_STUDENT']])
                    ->orWhereHas('laMissionAssigns', function ($query) {
                        $query->where('user_id', Auth::user()->id);
                    });
                });
            }

            if ($request->search_title) {
                $missions->whereRaw(
                    "LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, '$.en'))) LIKE ?",
                    ['%' . strtolower($request->search_title) . '%']
                );
            }

            $missions = $missions->withCount(['missionCompletes' => function ($query) {
                    $query->where('user_id', Auth::user()->id);
                }])
                ->orderByRaw('CASE WHEN mission_completes_count = 0 THEN 0 ELSE 1 END ASC')
                ->orderBy('index')
                ->paginate(100);

            $response['missions'] = LaMissionResource::collection($missions)->response()->getData(true);
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
            $validate = [
                'la_mission_id' => ['required', 'exists:la_missions,id'],
                'media'         => ['required'],
                'description'   => ['nullable'],
                'timing'        => ['required'],
            ];

            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }

            // 🔎 1️⃣ Look specifically for a skipped record first
            $skipped = LaMissionComplete::where('user_id', Auth::id())
                ->where('la_mission_id', $request->la_mission_id)
                ->where('status', 'skipped')
                ->first();

            // 🔎 2️⃣ Check for any submitted/completed record to block duplicates
            $existingSubmitted = LaMissionComplete::where('user_id', Auth::id())
                ->where('la_mission_id', $request->la_mission_id)
                ->whereIn('status', ['submitted','completed'])
                ->exists();

            if ($existingSubmitted) {
                return response()->json([
                    'error' => 'Mission already submitted.'
                ], 400);
            }

            // 📁 Upload media
            $mediaId = null;
            if ($request->hasFile('media')) {
                $missionCompleteImg = $request->file('media');
                $qMediaName = $missionCompleteImg->getClientOriginalName();
                $qMediaPath = Storage::put('media', $missionCompleteImg);

                $missionResourceMedia = Media::create([
                    'name' => $qMediaName,
                    'path' => $qMediaPath
                ]);

                $mediaId = $missionResourceMedia->id;
            }

            if ($skipped) {
                // ✅ 3️⃣ Update the skipped record
                $skipped->update([
                    'media_id'    => $mediaId,
                    'description' => $request->description,
                    'timing'      => $request->timing,
                    'status'      => 'submitted',
                ]);
            } else {
                // ✅ 4️⃣ No skipped record → create new
                LaMissionComplete::create([
                    'user_id'       => Auth::id(),
                    'la_mission_id' => $request->la_mission_id,
                    'media_id'      => $mediaId,
                    'description'   => $request->description,
                    'timing'        => $request->timing,
                    'status'        => 'submitted',
                ]);
            }

            return $this->sendResponse("", "Mission submitted successfully");

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

    public function skipMission(Request $request, $missionId)
    {
        try {
            $mission = LaMission::find($missionId);
            if (!$mission) {
                return $this->sendError('Mission not found');
            }

            $user = $request->user();

            // Find existing completion
            $missionComplete = LaMissionComplete::where('la_mission_id', $mission->id)
                ->where('user_id', $user->id)
                ->first();

            if ($missionComplete) {
                $dbStatus = $missionComplete->getRawOriginal('status'); // actual enum from DB

                if ($dbStatus === 'skipped') {
                    return $this->sendResponse($missionComplete, 'Mission already skipped');
                }

                if (in_array($dbStatus, ['approved', 'rejected', 'submitted'])) {
                    return $this->sendResponse($missionComplete, 'Mission already attempted');
                }
            }

            // Otherwise mark it as skipped
            if (!$missionComplete) {
                $missionComplete = new LaMissionComplete();
                $missionComplete->la_mission_id = $mission->id;
                $missionComplete->user_id = $user->id;
            }

            $missionComplete->status = 'skipped';
            $missionComplete->save();

            return $this->sendResponse($missionComplete, 'Mission skipped successfully');
        } catch (\Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }

    public function notifyMissionStatus(Request $request, $missionId)
    {
        $request->validate([
            'status'   => 'required|in:approved,rejected',
            'user_id'  => 'required|integer|exists:users,id',
        ]);

        $mission = LaMission::findOrFail($missionId);
        $user    = User::findOrFail($request->user_id);

        if ($request->status === 'approved') {
            $mission->sendApproveNotification($user);
        } else {
            $mission->sendRejectNotification($user);
        }

        return response()->json(['success' => true]);
    }

}