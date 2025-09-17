<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\UserResource;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use App\Models\Mission;
use App\Models\MissionComplete;
use App\Models\MissionImage;
use App\Models\MissionUserTiming;
use App\Models\User;
use App\Models\UserMissionComplete;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $data['email'])->first();

        if ($user && Hash::check($data['password'], $user->password)) {
            $token = $user->createToken('LifeApp')->accessToken;
            return new JsonResponse(['token' => $token], Response::HTTP_OK);
        }

        return response()->json([
            'error' => "Invalid username / password"
        ], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $query = User::query();
        if ($request->has('city')) {
            $query->where('users.city', $request->get('city'));
        }

        if ($request->has('state')) {
            $query->where('users.state', $request->get('state'));
        }

        $query->select(
            'users.id',
            'users.name',
            'users.grade',
            'users.address',
            DB::raw("JSON_OBJECT('id', schools.id, 'name', schools.name) as school"),
            DB::raw("(SELECT count('id') from movie_completes where movie_completes.user_id=users.id) as topic_completed"),
            DB::raw("(SELECT count('id') from mission_completes where mission_completes.user_id=users.id AND approved_at is not null) as mission_completed"),
            DB::raw("JSON_OBJECT('id', missions.id, 'name', missions.mission_name) as mission_submit")
        )->leftJoin('schools', 'users.school_id', '=', 'schools.id')
            ->leftJoin('mission_completes', 'mission_completes.user_id', '=', 'users.id')
            ->leftJoin('missions', 'missions.id', '=', 'mission_completes.mission_id')
            ->whereNull('mission_completes.approved_at');

        $users = $query->orderBy('users.id', 'desc')
            ->paginate(25);

        return UserResource::collection($users);
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function missionCompleted(User $user)
    {
        $missionCompletes = MissionComplete::where('user_id', $user->id)->get();
        $data = [];
        foreach ($missionCompletes as $missionComplete) {
            $userPoint = UserMissionComplete::where('mission_id', $missionComplete->mission->id)
                ->where('user_id', $user->id)
                ->latest()
                ->first();

            $upload = $user->missionUploads()->where('mission_id', $missionComplete->mission->id)->latest()->first();
            $missionMedia = MissionImage::where('mission_id', $missionComplete->mission->id)->first();

            $timings = MissionUserTiming::where('mission_id', $missionComplete->mission->id)
                ->where('user_id', $user->id)
                ->groupBy('mission_img_id')
                ->get();

            $data[] = [
                'id' => $missionComplete->id,
                'rating' => $missionComplete->rating,
                'created_at' => $missionComplete->created_at,
                'approved_at' => $missionComplete->apporved_at,
                'rejected_at' => $missionComplete->rejected_at,
                'comment' => $missionComplete->comment,
                'mission' => [
                    'id' => $missionComplete->mission->id,
                    'name' => $missionComplete->mission->mission_name,
                    'type' => $missionComplete->mission->mission_type,
                    'brain_points' => $missionComplete->mission->brain_points,
                    'heart_points' => $missionComplete->mission->heart_points,
                ],
                'points' => $userPoint ? $userPoint->earn_points : null,
                'upload_media' => $upload ? new MediaResource(Media::find($upload->question_media_id)) : null,
                'mission_media' => new MediaResource($missionMedia->media),
                'user_timings' => $timings,
            ];
        }
        return response()->json($data);
    }
}
