<?php

namespace App\Http\Controllers\Api\V3;

use App\Enums\GameType;
use App\Enums\StatusEnum;
use App\Enums\UserType;
use App\Http\Resources\API\V3\LaTopicResource;
use App\Models\LaTopic;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LaTopicController extends ResponseController
{
    public function index(Request $request)
    {
        try {
            $validate = array(
                'type' => ['required', Rule::in([GameType::QUIZ, GameType::RIDDLE, GameType::PUZZLE])],
                'la_level_id' => ['nullable', 'exists:la_levels,id'],
                'la_subject_id' => ['nullable', 'exists:la_subjects,id'],
            );
            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }
            $laTopics = LaTopic::where('status', StatusEnum::ACTIVE)->where('type', $request->type);

            if ($request->la_level_id) {
                $laTopics->where('la_level_id', $request->la_level_id);
            }

            if ($request->la_subject_id) {
                $laTopics->where('la_subject_id', $request->la_subject_id);
            }

            if (Auth::user()->type == UserType::Teacher) {
                $laTopics->where('allow_for', GameType::ALLOW_FOR['BY_TEACHER']);
            } else if (Auth::user()->type == UserType::Student) {
                $laTopics->where(function ($query) use ($request) {
                    $query->where('allow_for', GameType::ALLOW_FOR['ALL'])->orwhereHas('laTopicAssigns', function ($query) use ($request) {
                        $query->where('user_id', Auth::user()->id)->where('type', $request->type);
                    });
                });
            }
            $laTopics = $laTopics->get();
            $response['laTopics'] =  LaTopicResource::collection($laTopics);
            return $this->sendResponse($response, "Topics");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
