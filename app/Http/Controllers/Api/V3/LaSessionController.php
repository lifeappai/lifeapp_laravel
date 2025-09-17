<?php

namespace App\Http\Controllers\Api\V3;

use App\Enums\StatusEnum;
use App\Enums\ZoomEnum;
use App\Http\Resources\API\V3\LaSessionResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\LaSession;
use App\Models\LaSessionParticipant;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LaSessionController extends ResponseController
{
    public function create(Request $request)
    {
        try {
            $validate = [
                'heading' => ['required'],
                'description' => ['required'],
                'date' => ['required'],
                'time' => ['required'],
            ];
            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }

            // Create Zoom meeting
            $sessionLink = $this->createSessionLink($request);
            if ($sessionLink['status'] != 201) {
                return $this->sendError('Zoom link not created', $sessionLink['status']);
            }

            $laSession = null;

            DB::transaction(function () use ($request, $sessionLink, &$laSession) {
                // Store session
                $data = $request->all();
                $data['zoom_link']     = $sessionLink['result']['join_url'] ?? null;
                $data['zoom_password'] = $sessionLink['result']['password'] ?? null;
                $data['date_time']     = date('Y-m-d', strtotime($request->date)) . ' ' . date('H:i:s', strtotime($request->time));
                $data['user_id']       = Auth::id();

                $laSession = LaSession::create($data);

                // Create/Update campaign linked to this session
                DB::table('la_campaigns')->insert([
                    'game_type'     => 8,
                    'reference_id'  => $laSession->id,
                    'title'         => $laSession->heading,
                    'description'   => $laSession->description,
                    'button_name'   => 'Book',
                    'media_id'      => 121755,
                    'scheduled_for' => now()->toDateString(),
                    'status'        => 0,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            });

            $response = new LaSessionResource($laSession);
            return $this->sendResponse($response, "Session Created");
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function update(LaSession $laSession, Request $request)
    {
        try {
            $validate = [
                'heading' => ['required'],
                'description' => ['required'],
            ];
            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }

            // ✅ Update la_sessions
            $laSession->heading = $request->heading;
            $laSession->description = $request->description;
            $laSession->save();

            // ✅ Update related la_campaign (if exists)
            DB::table('la_campaigns')
                ->where('session_id', $laSession->id)
                ->update([
                    'heading' => $request->heading,
                    'description' => $request->description,
                    'updated_at' => now(),
                ]);

            $response = new LaSessionResource($laSession);
            return $this->sendResponse($response, "Session Updated");
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function mySessions()
    {
        try {
            date_default_timezone_set('Asia/Kolkata');
            $laSessions = LaSession::orderBy('id', 'desc')->where('user_id', Auth::user()->id);
            $laSessions = $laSessions->paginate();
            $response['sessions']  = LaSessionResource::collection($laSessions)->response()->getData(true);
            return $this->sendResponse($response, "My Sessions");
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
    public function upcomingSessions(Request $request)
    {
        try {
            date_default_timezone_set('Asia/Kolkata');
            $laSessions = LaSession::where('status', StatusEnum::ACTIVE)->where('date_time', '>', date('Y-m-d H:i:s'))->orderBy('id', 'desc');
            if ($request->user_id) {
                $laSessions->where('user_id', $request->user_id);
            }
            $laSessions = $laSessions->paginate();
            $response['sessions']  = LaSessionResource::collection($laSessions)->response()->getData(true);
            return $this->sendResponse($response, "Upcoming Sessions");
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function getSession(LaSession $laSession)
    {
        try {
            $response = new LaSessionResource($laSession);
            return $this->sendResponse($response, "Session Details");
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function sessionParticipate(LaSession $laSession)
    {
        try {
            LaSessionParticipant::firstOrCreate([
                "user_id" => Auth::user()->id,
                "la_session_id" => $laSession->id
            ]);
            return $this->sendResponse("", "User Participant Session");
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function attendSessions()
    {
        try {
            $attendSessions = LaSession::whereHas('laSessionParticipant', function ($query) {
                $query->where('user_id', Auth::user()->id);
            })->where('date_time', '<', date('Y-m-d H:i:s'))->paginate();
            $response['sessions'] = LaSessionResource::collection($attendSessions)->response()->getData(true);
            return $this->sendResponse($response, "Attended Sessions");
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function createSessionLink(Request $request)
    {
        $accessToken = $this->generateZoomToken();
        $dateTime = date('Y-m-d H:i', strtotime($request->date . ' ' . $request->time));
        $date = new \DateTime($dateTime);
        $startTime =    $date->format('Y-m-d\TH:i:s');
        $apiURL = "https://api.zoom.us/v2/users/me/meetings";
        $postInput = [
            'topic' => $request->heading,
            'type' => ZoomEnum::MEETING_TYPE_SCHEDULE,
            'start_time' => $startTime,
            'agenda' => $request->description,
            'settings' => [
                'host_video' => false,
                'participant_video' => false,
                'waiting_room' => true,
            ]
        ];
        $headers = [
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ];
        $response = Http::withHeaders($headers)->post($apiURL, $postInput);
        $statusCode = $response->status();
        $responseBody = json_decode($response->getBody(), true);
        $data = [
            'status' => $statusCode,
            'result' => $responseBody,
        ];
        return $data;
    }

    public function generateZoomToken()
    {
        $key = env('ZOOM_API_KEY');
        $secret = env('ZOOM_API_SECRET');
        $clientId = env('ZOOM_CLIENT_ID');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://zoom.us/oauth/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Host: zoom.us',
            'Authorization: Basic ' . base64_encode($key . ':' . $secret),
            'Content-Type: application/x-www-form-urlencoded',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=account_credentials&account_id=' . $clientId);
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response, true);
        return $data['access_token'];
    }
}
