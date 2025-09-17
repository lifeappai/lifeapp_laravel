<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Mission;
use App\Models\MissionUserTiming;
use App\Models\MissionUpload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\MissionResource;
use App\Http\Resources\MissionUploadResource;
use App\Models\MissionComplete;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\MediaUpload;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class MissionController extends Controller
{
	use MediaUpload;

	/**
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function index(Request $request): JsonResponse
    {
        $data = $request->validate([
                "locale" => ['required', 'string'],
            ]
        );
        return new JsonResponse([
            'missions' => MissionResource::collection(Mission::all())
        ],
            Response::HTTP_OK
        );
    }

	/**
	 * @return JsonResponse
	 */
	public function uploadMissionDocument(Request $request): JsonResponse
    {
        $data = $request->validate([
            "mission_id" => ['required', 'integer'],
            'media' => ['required'],
        ]);

        $missionComplete = MissionComplete::where('user_id', Auth::id())->where('mission_id', $data['mission_id'])->latest()->first();
        if ($missionComplete && ($missionComplete->isInReview() || $missionComplete->isApproved())) {
            return response()->json([
                'error' => 'Mission already submitted.'
            ], 400);
        }

        $media = $this->upload($data['media']);

        $missupload = MissionUpload::create([
            'mission_id' => $data['mission_id'],
            'user_id' => Auth::id(),
            'question_media_id' => $media->id,
        ]);

        return new JsonResponse([
            'data' => new MissionUploadResource($missupload),
            'message' => "Mission Upload successfully",
        ], Response::HTTP_OK);
    }

	/**
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function updateMissionUserTiming(Request $request): JsonResponse
    {
        $data = $request->validate([
            "mission_id" => ['required', 'integer'],
            "mission_img_id" => ['required', 'integer'],
            'timing' => ['required', 'string'],
        ]);

        Log::info("Mission User Time: " . json_encode($data));

        $missionComplete = MissionComplete::where('user_id', Auth::id())->where('mission_id', $data['mission_id'])->latest()->first();
        if ($missionComplete && ($missionComplete->isInReview() || $missionComplete->isApproved())) {
            return response()->json([
                'error' => 'Mission already submitted.'
            ], 400);
        }

        MissionUserTiming::create([
            'mission_id' => $data['mission_id'],
            'mission_img_id' => $data['mission_img_id'],
            'user_id' => Auth::id(),
            'timing' => $data['timing']
        ]);

        return new JsonResponse([
            'message' => "Mission timing added successfully",
        ], Response::HTTP_OK);
    }

	/**
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function completeMission(Request $request): JsonResponse
    {
        $data = $request->validate([
            "mission_id" => ['required', 'integer'],
            'description' => ['required', 'string'],
        ]);

        $missionComplete = MissionComplete::where('user_id', Auth::id())->where('mission_id', $data['mission_id'])->latest()->first();

        if ($missionComplete && ($missionComplete->isInReview() || $missionComplete->isApproved())) {
            return response()->json([
                'error' => 'Mission already submitted.'
            ], 400);
        }


        MissionComplete::create([
                'mission_id' => $data['mission_id'],
                'user_id' => Auth::id(),
                'description' => $data['description'],
            ]
        );

        return new JsonResponse([
            'message' => "Mission Complete successfully",
        ], Response::HTTP_OK);
    }

    /**
     * @param Mission $mission
     * @return JsonResponse
     */
	public function userSubmissions(Mission $mission)
    {
        $submissions = $mission->missionCompletes()->with('user')->get();

        $data = [];
        foreach ($submissions as $submission) {

            $missionMedia = $mission->missionUploads()->with('media')
                ->ofUser($submission->user_id)->first();

            $data[] = [
                'id' => $submission->id,
                'mission_id' => $submission->mission_id,
                'description' => $submission->description,
                'user' => $submission->user,
                'document' => $missionMedia ? $missionMedia : null,
                'created_at' => $submission->created_at,
                'updated_at' => $submission->updated_at,
            ];
        }

        return new JsonResponse(['submissions' => $data], Response::HTTP_OK);
    }
}
