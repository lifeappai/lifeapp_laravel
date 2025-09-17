<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\V1\MovieCompleteRequest;
use App\Http\Requests\Auth\V1\TopicRequest;
use App\Http\Requests\Auth\V1\MovieRequest;
use App\Http\Requests\Auth\V1\QuizRequest;
use App\Http\Resources\HallOfFrameResource;
use App\Http\Resources\MissionUploadResource;
use App\Http\Resources\MovieCompleteResource;
use App\Http\Resources\PublicUserResrouce;
use App\Models\MissionUpload;
use App\Models\Subjects;
use App\Models\Quiz;
use App\Models\Movie;
use App\Models\Topics;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Http\Resources\SubjectResource;
use App\Http\Resources\MovieResource;
use App\Http\Resources\QuizResource;
use App\Http\Resources\TopicResource;
use App\Http\Traits\CoinTrait;
use App\Models\MovieComplete;
use App\Models\QuestionAttempt;
use App\Models\MovieUserTiming;
use App\Models\QuizQuestion;
use App\Models\User;
use App\Models\SubjectTranslation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Question\Question;

class SubjectController extends Controller
{
    use CoinTrait;

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $data = $request->validate([
            "locale" => ['required', 'string'],
        ]);
        return new JsonResponse([
            'subjects' => SubjectResource::collection(
                SubjectTranslation::where(['locale' => $data['locale']])->orderBy('subject_id', 'ASC')->get()
            )
        ], Response::HTTP_OK);
    }

    /**
     * @param TopicRequest $request
     *
     * @return JsonResponse
     */
    public function getTopics(TopicRequest $request): JsonResponse
    {
        $data = $request->validated();
        return new JsonResponse([
            'topics' => TopicResource::collection(
                Topics::select('topics.*', 'topic_translations.locale', 'topic_translations.title', 'topic_translations.description')
                    ->join('topic_translations', 'topics.id', '=', 'topic_translations.topic_id')
                    ->where(['topic_translations.locale' => $data['locale']])
                    ->where(['topics.level_id' => $data['level_id']])
                    ->orderBy('topics.id', 'ASC')
                    ->get()
            )
        ], Response::HTTP_OK);
    }

    /**
     * @param MovieRequest $request
     *
     * @return JsonResponse
     */
    public function getMovie(MovieRequest $request): JsonResponse
    {
        $data = $request->validated();

        return new JsonResponse([
            'movie' => MovieResource::collection(
                Movie::where(['topic_id' => $data['topic_id'], 'locale' => $data['locale']])->get()
            )
        ], Response::HTTP_OK);
    }

    /**
     * @return JsonResponse
     */
    public function getQuiz(QuizRequest $request): JsonResponse
    {
        $data = $request->validated();

        return new JsonResponse([
            'quiz' => QuizResource::collection(
                Quiz::where(['movie_id' => $data['movie_id'], 'locale' => $data['locale']])->get()
            )
        ], Response::HTTP_OK);
    }

    /**
     * @return JsonResponse
     */
    public function updateVideoTiming(Request $request): JsonResponse
    {
        $data = $request->validate([
            "movie_id" => ['required', 'integer'],
            'duration' => ['required', 'integer'],
            'no_of_view' => ['required', 'string'],
            'rating' => ['integer'],
            'earn_point' => ['integer'],
        ]);
        $user = Auth::user();

        $movie = Movie::where('id', $data['movie_id'])->first();
        $completedmovieids = Movie::where('topic_id', $movie->topic_id)->pluck('id')->toArray();
        $movieTime = MovieUserTiming::whereIn('movie_id',$completedmovieids)
            ->where('user_id', $user->id)->first();

        if ($movieTime) {
            $response = [
                'message' => "Movie Timing Update successfully",
                'data' => [
                    'rating' => $movieTime->rating,
                    'point_type' => $movieTime->point_type,
                    'earn_point' => $movieTime->earn_point,
                ]
            ];
            return new JsonResponse($response, Response::HTTP_OK);
        }

        $point = $movie->brain_points + $movie->heart_points;
        $totalDuration = $movie->duration + $movie->after_duration;

        if ($data['duration'] < $totalDuration) {
            $rating = 1;
        } elseif ($data['duration'] > $totalDuration) {
            $rating = 3;
        } else {
            $rating = 5;
        }

        $earnPoint = $this->coinCalculation($point, $rating);
        MovieUserTiming::updateOrCreate([
            'movie_id' => $data['movie_id'],
            'user_id' => $user->id,
        ], [
            'duration' => $data['duration'],
            'no_of_view' => $data['no_of_view'],
            'rating' => $rating,
            'point_type' => $movie->movie_type,
            'earn_point' => $earnPoint,
        ]);
        $response = [
            'message' => "Movie Timing Update successfully",
        ];
        return new JsonResponse($response, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function updateQuestionAttempt(Request $request): JsonResponse
    {
        $data = $request->validate([
            "quiz_id" => ['required', 'integer'],
            "question_id" => ['required', 'integer'],
            'no_of_attempt' => ['required', 'integer'],
            'rating' => ['integer'],
            'earn_point' => ['integer'],
        ]);

        Log::info('Request Data: ' . json_encode($data));

        $user = Auth::user();
        $quiz = Quiz::where(["id" => $data['quiz_id']])->first();
        $question = QuizQuestion::where(["id" => $data['question_id']])->first();
        $getAllQuestions = QuizQuestion::where('question_reference', $question->question_reference)->pluck('id')->toArray();
        $attempt = QuestionAttempt::where([
            'user_id' => $user->id,
        ])->whereIn('question_id', $getAllQuestions)->first();

        Log::info('Attempts: ' . json_encode($attempt));

        if ($attempt) {
            $response = [
                'message' => "Update answer history successfully",
                'data' => [
                    'rating' => $attempt->rating,
                    'point_type' => $question->type,
                    'earn_point' => $attempt->earn_point,
                ]
            ];
            return new JsonResponse($response, Response::HTTP_OK);
        }

        Log::info('Create Attempt');

        if ($question->type === "brain") {
            $totalBrainCount = QuizQuestion::where(["quiz_id" => $data['quiz_id'], "type" => $question->type])->count();
            $point = $quiz->brain_points / $totalBrainCount;
        } else {
            $totalHeartCount = QuizQuestion::where(["quiz_id" => $data['quiz_id'], "type" => $question->type])->count();
            $point = $quiz->heart_points / $totalHeartCount;
        }
        if ($data['no_of_attempt'] == 1) {
            $rating = 5;
        } elseif ($data['no_of_attempt'] == 2) {
            $rating = 3;
        } else {
            $rating = 1;
        }
        $earnPoint = $this->coinCalculation($point, $rating);

        QuestionAttempt::updateOrCreate([
            'user_id' => $user->id,
            'quiz_id' => $data['quiz_id'],
            'question_id' => $data['question_id'],
        ], [
            'no_of_attempt' => $data['no_of_attempt'],
            'rating' => $rating,
            'point_type' => $question->type,
            'earn_point' => (int)$earnPoint,
        ]);

        $response = [
            'message' => "Update answser history successfully",
            'data' => [
                'rating' => $rating,
                'earn_point' => (int)$earnPoint,
            ]
        ];
        return new JsonResponse($response, Response::HTTP_OK);
    }

    public function movieCompleted(MovieCompleteRequest $request): JsonResponse
    {
        $user = Auth::user();
        $data = $request->validated();
        $topic_id = Movie::where('id', $data['movie_id'])->pluck('topic_id')->first();
        $quiz = Quiz::where(['movie_id' => $data['movie_id']])->first();
        $movieIds = Movie::where('topic_id', $topic_id)->pluck('id')->toArray();

        $getMovieRating = MovieUserTiming::whereIn('movie_id', $movieIds)
            ->where('user_id', $user->id)->first();

        $movie_earnPoint = $getMovieRating->earn_point;
        $movie_rating = $getMovieRating->rating;
        $movie_point_type = $getMovieRating->point_type;

        $completeMovie = MovieComplete::whereIn('movie_id', $movieIds)
            ->where('user_id', $user->id)->first();

        if ($completeMovie) {
            return new JsonResponse([
                'earn-point' => [new MovieCompleteResource($completeMovie)]
            ], Response::HTTP_OK);
        }

        $quizIds = Quiz::whereIn('movie_id', $movieIds)->pluck('id')->toArray();

        $questionCount = $quiz->quizQuestions()->count();
        $attemptsCount = QuestionAttempt::whereIn('quiz_id', $quizIds)->where('user_id', $user->id)->count();

        if ($questionCount != $attemptsCount) {
            return new JsonResponse([
                'error' => "Answer all question of quiz."
            ], Response::HTTP_BAD_REQUEST);
        }

        $getQuizRating = QuestionAttempt::select(
            DB::raw("SUM(earn_point) as earn_point"), DB::raw("SUM(rating) as rating"),
            DB::raw("count(id) as no_question"),
            DB::raw("SUM(if(point_type = 'brain',earn_point,0)) as brain_earn_point"),
            DB::raw("SUM(if(point_type = 'heart',earn_point,0)) as heart_earn_point")
        )->where(['quiz_id' => $quiz->id, 'user_id' => $user->id])->get();

        $total_earn = $movie_earnPoint + $getQuizRating[0]->earn_point;
        $total_brain_points =
            ($movie_point_type == "brain" ? $movie_earnPoint : 0) + $getQuizRating[0]->brain_earn_point;
        $total_heart_points =
            ($movie_point_type == "heart" ? $movie_earnPoint : 0) + $getQuizRating[0]->heart_earn_point;
        $total_rating = ($movie_rating + $getQuizRating[0]->rating) / (1 + $getQuizRating[0]->no_question);

        $movieCompleted = MovieComplete::updateOrCreate([
            'user_id' => $user->id,
            'movie_id' => $data['movie_id'],
        ], [
            'earn_points' => $total_earn,
            'avg_rating' => $total_rating,
            'brain_points' => $total_brain_points,
            'heart_points' => $total_heart_points,
        ]);
        $id = $movieCompleted->id;

        return new JsonResponse([
            'earn-point' => MovieCompleteResource::collection(MovieComplete::where(["id" => $id])->get())
        ], Response::HTTP_OK);
    }

    public function hallOfFame(Request $request): JsonResponse
    {
        $data = $request->validate([
            "filter" => ['string']
        ]);

        if ($request->has('filter')) {

            $filter = $data["filter"];

            $userIds = implode(",", User::whereIn("mobile_no", [
                9969949630,
                7350295766,
                9969949630,
                8390071915,
                7745857864,
                7875614227,
                9324628212,
                8551919293,
                9491084185,
                7745857864
            ])->pluck("id")->toArray());

            if ($filter == 'all' or $filter == null) {
                $query = "SELECT movie_completes.user_id ,SUM(brain_points) as points FROM movie_completes INNER JOIN users ON users.id = movie_completes.user_id GROUP by movie_completes.user_id ORDER by points DESC LIMIT 1";
                Log::info("Brain Query: " . $query);
                $brainPoints = DB::select(DB::raw($query));

                $query = "SELECT movie_completes.user_id ,SUM(heart_points) as points FROM movie_completes INNER JOIN users ON users.id = movie_completes.user_id GROUP by movie_completes.user_id ORDER by points DESC LIMIT 1";
                Log::info("Heart Query: " . $query);
                $heartPoints = DB::select(DB::raw($query));

                $query = "SELECT user_id, mission_id, earn_points as points FROM user_mission_completes where user_id in ({$userIds}) GROUP by user_id ORDER by points DESC, user_mission_completes.created_at DESC LIMIT 1";
                Log::info("Mission Query: " . $query);
                $missionPoints = DB::select(DB::raw($query));

            } else {
                $query = "SELECT movie_completes.user_id ,SUM(brain_points) as points FROM movie_completes INNER JOIN users ON users.id = movie_completes.user_id where (users.city = '" . $filter . "' OR users.state = '" . $filter . "') GROUP by movie_completes.user_id ORDER by points DESC LIMIT 1";
                Log::info("Brain Query: " . $query);
                $brainPoints = DB::select(DB::raw($query));

                $query = "SELECT movie_completes.user_id ,SUM(heart_points) as points FROM movie_completes INNER JOIN users ON users.id = movie_completes.user_id WHERE (users.city = '" . $filter . "' OR users.state = '" . $filter . "') GROUP by movie_completes.user_id ORDER by points DESC LIMIT 1";
                Log::info("heart Query: " . $query);
                $heartPoints = DB::select(DB::raw($query));

                $query = "SELECT user_id, mission_id, earn_points as points FROM user_mission_completes INNER JOIN users ON users.id = user_mission_completes.user_id WHERE users.id in ({$userIds}) AND (users.city = '" . $filter . "' OR users.state = '" . $filter . "') ORDER by points DESC, user_mission_completes.created_at DESC LIMIT 1";
                Log::info("Mission Query: " . $query);
                $missionPoints = DB::select(DB::raw($query));
            }

            $mResponse = null;
            if (count($missionPoints) > 0 && isset($missionPoints[0]->user_id)) {
                $upload = MissionUpload::where('user_id', $missionPoints[0]->user_id)
                    ->where('mission_id', $missionPoints[0]->mission_id)->first();

                if ($upload) {
                    $mResponse = [
                        'total_point' => $missionPoints[0]->points,
                        'user' => new PublicUserResrouce(User::find($missionPoints[0]->user_id)),
                        'mission_upload' => new MissionUploadResource($upload),
                    ];
                }
            }

            return new JsonResponse([
                'brain_points_campaign' => HallOfFrameResource::collection($brainPoints),
                'heart_points_campaign' => HallOfFrameResource::collection($heartPoints),
                'mission_done' => $mResponse,
            ], Response::HTTP_OK);

        } else {
            return new JsonResponse([
                'message' => "Select atleast one filter",
            ], Response::HTTP_OK);
        }
    }
}
