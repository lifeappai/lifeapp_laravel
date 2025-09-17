<?php

namespace App\Http\Controllers\Api\V3;

use App\Constants\NotificationTemplate;
use App\Enums\GameType;
use App\Enums\NotificationAction;
use App\Enums\QuizGameParticipantStatusEnum;
use App\Enums\QuizGameStatusEnum;
use App\Enums\StatusEnum;
use App\Http\Resources\API\V3\LaQuestionResource;
use App\Http\Resources\API\V3\LaQuizGameParticipantResource;
use App\Http\Resources\API\V3\LaQuizGameResource;
use App\Http\Resources\API\V3\LaQuizGameResultResource;
use App\Http\Resources\MediaResource;
use App\Http\Resources\PublicUserResrouce;
use App\Models\CoinTransaction;
use App\Models\LaLevel;
use App\Models\LaQuestion;
use App\Models\LaQuiz;
use App\Models\LaQuizGame;
use App\Models\LaQuizGameParticipant;
use App\Models\LaQuizGameQuestionAnswer;
use App\Models\LaQuizGameResult;
use App\Models\User;
use App\Notifications\SendPushNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LaQuizGameController extends ResponseController
{
    public function createQuiz(Request $request)
    {
        try {

            $validate = array(
                'la_subject_id' => ['required', 'exists:la_subjects,id'],
                'la_topic_id' => ['required', 'exists:la_topics,id'],
                'la_level_id' => ['required', 'exists:la_levels,id'],
                'type' => ['required', Rule::in([GameType::QUIZ, GameType::RIDDLE, GameType::PUZZLE])],
            );
            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }
            $data = $request->all();
            $data['user_id'] = Auth::user()->id;
            $data['game_code'] = rand("0000", "9999");

            $data['time'] = 0;
            $laLevel = LaLevel::find($request->la_level_id);
            if ($request->type == GameType::QUIZ) {
                $data['time'] = $laLevel->quiz_time;
            } elseif ($request->type == GameType::PUZZLE) {
                $data['time'] = $laLevel->puzzle_time;
            } elseif ($request->type == GameType::RIDDLE) {
                $data['time'] = $laLevel->riddle_time;
            }

            $quizGameId = 0;
            $checkPreviousGame = LaQuizGame::orderBy('id', 'desc')->first();
            if ($checkPreviousGame) {
                $quizGameId = $checkPreviousGame->id;
            }
            $data['game_code'] = str_pad($quizGameId, 4, '0', STR_PAD_LEFT);
            $laQuizGame = LaQuizGame::create($data);

            LaQuizGameParticipant::create([
                "la_quiz_game_id" => $laQuizGame->id,
                "user_id" => $data['user_id'],
                "status" => 2,
            ]);
            $quizGame = new LaQuizGameResource($laQuizGame);
            return $this->sendResponse($quizGame, "Quiz Game Create successfully");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function addQuizGameParticipants(LaQuizGame $laQuizGame, Request $request)
    {
        try {
            $validate = array(
                'user_id' => ['required', 'exists:users,id'],
                'game_code' => ['required'],
            );
            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }

            $checkGameCode = $laQuizGame->where('game_code', $request->game_code)->first();
            if (!$checkGameCode) {
                return $this->sendError("Code Not Found");
            }

            $checkGameParticipant = LaQuizGameParticipant::whereHas('laQuizGame')->where('user_id', $request->user_id)->first();

            if ($checkGameParticipant) {
                return $this->sendError("Participant Already Added");
            }

            LaQuizGameParticipant::create([
                "la_quiz_game_id" => $laQuizGame->id,
                "user_id" => $request->user_id,
                "status" => 2,
            ]);

            return $this->sendResponse("", "Participant Added");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function getQuizGameParticipants(LaQuizGame $laQuizGame)
    {
        try {
            $laQuizGameParticipants = LaQuizGameParticipant::where('la_quiz_game_id', $laQuizGame->id)->get();
            $participants = LaQuizGameParticipantResource::collection($laQuizGameParticipants);
            $gameStatus = $laQuizGame->status;
            return $this->sendResponse([
                "participants" => $participants,
                "game_status" => $gameStatus,
            ], "Quiz Game Participants List");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function changeQuizGameParticipantUser(Request $request, LaQuizGame $laQuizGame)
    {
        try {
            $validate = array(
                'status' => ['required', Rule::in([QuizGameParticipantStatusEnum::ACCEPT, QuizGameParticipantStatusEnum::REJECT])],
            );
            $validator = Validator::make($request->all(), $validate);

            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }

            if ($laQuizGame->status != QuizGameStatusEnum::PENDING) {
                return $this->sendUnauthorizedError();
            }

            $userParticipationCheck = LaQuizGameParticipant::where('la_quiz_game_id', $laQuizGame->id)
                ->where('user_id', $request->user()->id)->first();

            if (!$userParticipationCheck) {
                return $this->sendError("User Is Not Invited In This Quiz Game");
            }

            $userParticipationCheck->status = $request->status;
            $userParticipationCheck->save();

            $user = Auth::user();

            $participants = $laQuizGame->participants()
                ->where('users.id', '!=', $user->id)
                ->whereNotNull('device_token')
                ->get();

            foreach ($participants as $participant) {

                $notification = $request->status == QuizGameParticipantStatusEnum::ACCEPT ?
                    NotificationTemplate::QUIZ_GAME_INVITE_ACCEPT : NotificationTemplate::QUIZ_GAME_INVITE_REJECT;

                $pushNotification = new SendPushNotification(
                    $notification['title'],
                    sprintf($notification['body'], $user->name),
                    [$participant->device_token],
                    [
                        'action' => NotificationAction::QuizGame(),
                        'action_id' => $laQuizGame->id,
                        'quiz_time' => (int)$laQuizGame->time,
                        'status' => $request->status == QuizGameParticipantStatusEnum::ACCEPT ? 'accepted' : 'rejected'
                    ]
                );

                $participant->notify($pushNotification);
            }

            $participate = new LaQuizGameParticipantResource($userParticipationCheck);

            return $this->sendResponse($participate, "Quiz Game User Participant Status Change");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function startQuizGame(Request $request, LaQuizGame $laQuizGame)
    {
        try {
            if (
                $laQuizGame->status === QuizGameStatusEnum::PENDING
                && $request->user()->id != $laQuizGame->user_id
            ) {
                Log::info("Unauthorized {$laQuizGame->id}: Level 1");
                return $this->sendUnauthorizedError();
            }

            if ($laQuizGame->status == QuizGameStatusEnum::EXPIRED) {
                Log::info("Unauthorized {$laQuizGame->id}: Level 2");
                return $this->sendError("Quiz is expired", 400);
            }

            $laQuizGame->status = QuizGameStatusEnum::INPROGRESS;
            if ($laQuizGame->started_at == null) {
                $laQuizGame->started_at = Carbon::now();

                foreach ($laQuizGame->participants()->whereNotNull('device_token')->get() as $participant) {

                    if ($participant->id !== $request->user()->id) {
                        $pushNotification = new SendPushNotification(
                            "Life App",
                            sprintf("%s started the game.", $request->user()->name),
                            [$participant->device_token],
                            [
                                'action' => NotificationAction::QuizGame(),
                                'action_id' => $laQuizGame->id,
                                'quiz_time' => (int)$laQuizGame->time,
                                'game_status' => $laQuizGame->status
                            ]
                        );

                        $participant->notify($pushNotification);
                    }
                }
            }

            $laQuestions = LaQuestion::where('la_subject_id', $laQuizGame->la_subject_id)
                ->whereNotNull('answer_option_id')
                ->whereHas('questionOptions')
                ->where('la_topic_id', $laQuizGame->la_topic_id)
                ->where('type', $laQuizGame->type)
                ->where('status', StatusEnum::ACTIVE)
                ->where('la_level_id', $laQuizGame->la_level_id);

            if (!empty($laQuizGame->questions)) {
                $laQuestions = $laQuestions->whereIn('id', $laQuizGame->questions)->paginate(50);
            } else {
                $laQuestions = $laQuestions->inRandomOrder()->paginate(50);
                $laQuizGame->questions = $laQuestions->pluck("id")->toArray();
            }

            $laQuizGame->save();

            $data = LaQuestionResource::collection($laQuestions)->response()->getData(true);

            return $this->sendResponse($data, "Start Game Quiz");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function endQuizGame(LaQuizGame $laQuizGame, Request $request)
    {
        if ($laQuizGame->status == QuizGameStatusEnum::EXPIRED) {
            return $this->sendError("Quiz is expired", 400);
        }

        try {
            $user = $request->user();
            $validate = array(
                'answers' => ['required', 'array'],
            );
            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }

            foreach ($request->answers as $answer) {

                $question = LaQuestion::where('id', $answer['question_id'])->first();
                $correctAnswer = 0;
                $coins = 0;

                if ($answer['answer_id'] == $question->answer_option_id) {
                    $correctAnswer = 1;
                    if ($question->laLevel) {
                        $coins = 0;
                        if ($laQuizGame->type == GameType::QUIZ) {
                            $coins = $question->laLevel->quiz_points;
                        } elseif ($laQuizGame->type == GameType::PUZZLE) {
                            $coins = $question->laLevel->puzzle_points;
                        } elseif ($laQuizGame->type == GameType::RIDDLE) {
                            $coins = $question->laLevel->riddle_points;
                        }
                    }
                }

                LaQuizGameQuestionAnswer::firstOrCreate([
                    "la_quiz_game_id" => $laQuizGame->id,
                    "la_question_id" => $question->id,
                    "user_id" => Auth::user()->id
                ], [
                    "la_question_option_id" => $answer['answer_id'],
                    "is_correct" => $correctAnswer,
                    "coins" => $coins,
                ]);
            }

            $userEarnedCoins = LaQuizGameQuestionAnswer::where("la_quiz_game_id", $laQuizGame->id)->sum("coins");
            $correctAnswers = LaQuizGameQuestionAnswer::where("la_quiz_game_id", $laQuizGame->id)
                ->where("is_correct", 1)->count();

            $result = LaQuizGameResult::create([
                "la_quiz_game_id" => $laQuizGame->id,
                "user_id" => Auth::user()->id,
                "total_questions" => count($request->answers),
                "total_correct_answers" => $correctAnswers,
                "coins" => $userEarnedCoins,
            ]);

            $userPlayCount = LaQuizGame::where('user_id', Auth::user()->id)
                ->where('la_level_id',  $laQuizGame->la_level_id)
                ->where('la_subject_id',  $laQuizGame->la_subject_id)
                ->where('la_topic_id',  $laQuizGame->la_topic_id)
                ->where('type',   GameType::QUIZ)
                ->whereNotNull('completed_at')
                ->count();

            if ($userPlayCount == 0) {
                $user->createTransaction($result, $userEarnedCoins, CoinTransaction::TYPE_QUIZ);
            }

            $laQuizGame->completed_at = Carbon::now();
            $laQuizGame->save();

            $result['user_earned_coins'] = $userEarnedCoins;
            $result['correct_answers'] = $correctAnswers;

            return $this->sendResponse($result, "Quiz Complete");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function quizGameAnswers(Request $request, LaQuizGame $laQuizGame)
    {
        $user = $request->user();

        $questions = $laQuizGame->laQuestions()
            ->where('la_quiz_game_question_answers.user_id', $user->id)
            ->with('options')
            ->get();

        $questions = $questions->map(function ($question) {
            $question->title = $question->title['en'] ?? null;
            if ($question->title && $question->type == GameType::PUZZLE && $question->question_type == GameType::QUESTION_TYPE['IMAGE']) {
                $media = $question->getMedia($question->title);
                $question->title = new MediaResource($media);
            }
            $question->options = $question->options->map(function ($option) {
                $option->title = $option->title['en'] ?? null;
                return $option;
            });
            return $question;
        });
        $invite = LaQuizGameParticipant::where('la_quiz_game_id', $laQuizGame->id)->where('user_id', $user->id)->first();

        return response()->json([
            'status' => Response::HTTP_OK,
            'data' => $questions,
            'quiz_game' => [
                "id" => $laQuizGame->id,
                "status" => $laQuizGame->status,
                "game_participant_status" => $invite->status ?? null,
            ],
            'message' => 'Quiz Review'
        ], Response::HTTP_OK);
    }

    public function getQuizGameResult(LaQuizGame $laQuizGame)
    {
        try {
            $laQuizGameWinners = LaQuizGameResult::where('la_quiz_game_id', $laQuizGame->id)->orderBy('coins', 'desc')->get();
            $participants = LaQuizGameResultResource::collection($laQuizGameWinners);
            return $this->sendResponse($participants, "Quiz Game Participants List");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function quizGameHistory()
    {
        try {
            $laQuizGames = LaQuizGame::whereHas('quizGameParticipants', function ($query) {
                $query->where('user_id', Auth::user()->id)->where('status', QuizGameParticipantStatusEnum::ACCEPT);
            })->latest()->paginate(25);
            $participants = LaQuizGameResource::collection($laQuizGames)->response()->getData(true);
            return $this->sendResponse($participants, "Quiz Game History");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function show(LaQuizGame $laQuizGame)
    {
        try {
            return $this->sendResponse(new LaQuizGameResource($laQuizGame), 'Quiz Details');
        } catch (\Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }
}
