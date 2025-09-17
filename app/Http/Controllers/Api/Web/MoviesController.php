<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\V1\TopicRequest;
use App\Http\Requests\Auth\V1\MovieRequest;
use App\Http\Requests\Auth\V1\QuizRequest;
use App\Http\Requests\Auth\V1\SubjectRequest;
use App\Http\Resources\Web\TopicResource;
use App\Http\Resources\MovieResource;
use App\Http\Resources\QuizResource;
use App\Models\Subjects;
use App\Models\Levels;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Media;
use App\Models\Topics;
use App\Models\Movie;
use Illuminate\Support\Facades\Storage;
#use App\Http\Resources\SubjectResource;
use App\Http\Resources\Web\SubjectByIdResource;
use App\Http\Resources\Web\SubjectResource;
use App\Models\QuestionOptions;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Http\Traits\MediaUpload;
use App\Models\LevelTranslation;
use App\Models\SubjectTranslation;
use App\Models\TopicTranslation;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class MoviesController extends Controller
{
    use MediaUpload;
    /**
     * @return JsonResponse
     */
    public function index() : JsonResponse
    {
        return new JsonResponse(
            ['subjects' =>
                SubjectResource::collection(Subjects::get())
            ],
            Response::HTTP_OK);
    }


    public function getSubject(SubjectRequest $request): JsonResponse
    {
        $data = $request->validated();
        return new JsonResponse(['subjects' => SubjectResource::collection(Subjects::where(['id' => $data['subject_id']])->get())],Response::HTTP_OK);
    }

    /**
     * @return JsonResponse
    */
    public function getQuiz(QuizRequest $request) : JsonResponse
    {
        $data = $request->validated();

        return new JsonResponse(['quiz' => QuizResource::collection(Quiz::where(['movie_id' => $data['movie_id']])->get())],Response::HTTP_OK);
    }

    public function createSubject(Request $request) : JsonResponse
    {
        $data = $request->validate(
			[
				'name'  => 'required|string|unique:subjects',
                'flag'  => 'integer|nullable',
			]
		);
        $subject = Subjects::create($data);
        $response = [
            'data'      => $subject->id,
            'message'   => "Subject created successfully",
        ];
        return new JsonResponse($response, Response::HTTP_OK);
    }

    public function updateSubject(Request $request) : JsonResponse
    {
        $data = $request->validate(
			[
				'name'          => 'required|string|unique:subjects',
                'subject_id'    => 'required|integer',
			]
		);
        Subjects::where(['id' => $data["subject_id"]])->update(["name" => $data["name"]]);
        $response = [
            'message' => "Subject update successfully",
        ];
        return new JsonResponse($response, Response::HTTP_OK);
    }


    public function createLevel(Request $request) : JsonResponse
    {
        $data = $request->validate(
			[
				'subject_id'        => 'required|integer',
                'flag'              => 'integer|nullable',
                'level'             => 'required|integer',
                'description'       => 'string|nullable',
                'total_rewards'     => 'string|required',
                'total_question'    => 'string|required',
			]
		);
        $level = Levels::create($data);
        $response = [
            'data' => $level->id,
            'message' => "Level created successfully",
        ];
        return new JsonResponse($response, Response::HTTP_OK);
    }

    public function updateLevel(Request $request) : JsonResponse
    {
        $data = $request->validate(
			[
				'level_id'          => 'required|integer',
                'level'             => 'required|integer',
                'description'       => 'string|nullable',
                'total_rewards'     => 'string|required',
                'total_question'    => 'string|required',
			]
		);
        Levels::updateOrCreate(['id' => $data["level_id"]], $data);
        $response = [
            'message' => "Level update successfully",
        ];
        return new JsonResponse($response, Response::HTTP_OK);
    }

    public function createTopic(Request $request) : JsonResponse
    {
        $data = $request->validate(
			[
				'level_id'      => 'required|integer',
                'title'         => 'required|string',
                'flag'          => 'integer|nullable',
                'description'   => 'string|nullable',
                'image'         => 'required|mimes:jpeg,png,jpg',
			]
		);
        $media = $this->upload($data['image']);
        $data['topic_media_id'] = $media->id;
        $topic = Topics::create($data);
        $response = [
            'data' => $topic->id,
            'message' => "Topic created successfully",
        ];
        return new JsonResponse($response, Response::HTTP_OK);
    }

    public function updateTopic(Request $request) : JsonResponse
    {
        $data = $request->validate(
            [
                'topic_id'      => 'required|integer',
                'title'         => 'required|string',
                'description'   => 'string|nullable',
                'image'         => 'mimes:jpeg,png,jpg',
            ]
        );
        Topics::updateOrCreate(['id' => $data["topic_id"]], $data);
        if($request->hasFile('image')){
            $media = $this->upload($data['image']);
            Topics::where(['id' => $data["topic_id"]])->update([
                'topic_media_id'  => $media->id,
            ]);
        }
        $response = [
            'message' => "Topic update successfully",
        ];
        return new JsonResponse($response, Response::HTTP_OK);
    }

    public function createMovie(Request $request) : JsonResponse
    {
        $data = $request->validate(
			[
				'topic_id'        => 'required|integer',
                'title'           => 'required|string',
                'locale'          => 'string|nullable',
                'movie'           => 'required|nullable',
                'duration'        => 'string|nullable',
                'after_duration'  => 'string|nullable',
                'movie_type'      => 'string|nullable',
                'brain_points'    => 'string|nullable',
                'heart_points'    => 'string|nullable',
			]
		);
        $media = $this->upload($data['movie']);
        $data['movie_media_id'] = $media->id;
        $movie = Movie::create($data);
        $response = [
            'data'      => $movie->id,
            'message'   => "Movie created successfully",
        ];
        return new JsonResponse($response, Response::HTTP_OK);
    }

    public function updateMovie(Request $request) : JsonResponse
    {
        $data = $request->validate(
            [
                'movie_id' => 'required|integer',
                'title'    => 'required|string',
                'movie'    => 'nullable',
                'duration'  => 'string|nullable',
                'after_duration'  => 'string|nullable',
                'movie_type'  => 'string|nullable',
                'brain_points'  => 'string|nullable',
                'heart_points'  => 'string|nullable',
            ]
        );

        Movie::updateOrCreate(['id' => $data["movie_id"]], $data);
        if($request->hasFile('movie')){
            $media = $this->upload($data['movie']);
            Movie::where(['id' => $data["movie_id"]])->update([
                'movie_media_id'  => $media->id,
            ]);
        }
        $response = [
            'message' => "Movie update successfully",
        ];
        return new JsonResponse($response, Response::HTTP_OK);
    }


    public function createQuiz(Request $request) : JsonResponse
    {
        $data = $request->validate(
			[
				'movie_id' => 'required|integer',
                'locale' => 'required',
                'brain_points'    => 'required|integer',
                'heart_points'    => 'integer|integer',
			]
		);

        $quiz = Quiz::create($data);
        $response = [
            'data' => $quiz->id,
            'message' => "Quiz created successfully",
        ];
        return new JsonResponse($response, Response::HTTP_OK);
    }

    public function updateQuiz(Request $request) : JsonResponse
    {
        $data = $request->validate(
            [
                'quiz_id' => 'required|integer',
                'brain_points'    => 'required|integer',
                'heart_points'    => 'required|integer',
            ]
        );
        Quiz::updateOrCreate(['id' => $data["quiz_id"]], $data);

        $response = [
            'message' => "Quiz updated successfully",
        ];
        return new JsonResponse($response, Response::HTTP_OK);
    }
    /**
     * @return JsonResponse
     */
    public function getTopics(Request $request) : JsonResponse
    {
        $data = $request->validate(
			[
				'level_id' => 'required|integer'
			]
		);

        return new JsonResponse(['topics' => TopicResource::collection(Topics::where(['level_id' => $data['level_id']])->get())],Response::HTTP_OK);
    }

    public function getTopicById(Request $request): JsonResponse
    {
        $data = $request->validate(
			[
				'topic_id' => 'required|integer'
			]
		);
        return new JsonResponse(['topic' => TopicResource::collection(Topics::where(['id' => $data['topic_id']])->get())],Response::HTTP_OK);
    }

    /**
     * @return JsonResponse
     */
    public function getMovie(MovieRequest $request) : JsonResponse
    {
        $data = $request->validated();

        return new JsonResponse(['movie' => MovieResource::collection(Movie::where(['topic_id' => $data['topic_id']])->get())],Response::HTTP_OK);
    }

    public function createQuestion(Request $request) : JsonResponse
    {
        $data = $request->validate(
            [
                'quiz_id' => 'required|integer',
                'locale' => 'required|string',
                'title' => 'required|nullable',
                'type' => 'string|nullable',
                'audio' => 'nullable',
                'question_img' => 'required',
                'answer' => 'string|nullable',
            ]
        );

        $audioFile = $data['audio'];

        $mediaName = $audioFile->getClientOriginalName();
        $mediaPath = Storage::put('media', $audioFile);
        $audoMedia = Media::create(
            [
                'name' => $mediaName,
                'path' => $mediaPath
            ]
        );

        $questionImg = $data['question_img'];

        $qMediaName = $questionImg->getClientOriginalName();
        $qMediaPath = Storage::put('media', $questionImg);
        $questionMedia = Media::create(
            [
                'name' => $qMediaName,
                'path' => $qMediaPath
            ]
        );

        if ($data['locale'] != 'en' or $data['locale'] != 'En') {
            $quiz = Quiz::where('id', $request->quiz_id)->first();
            $movie = Movie::where('id', $quiz->movie_id)->first();
            $movies_id = Movie::where('topic_id', $movie->topic_id)->where('locale', 'en')->first();
            $quiz_id = Quiz::where('movie_id', $movies_id->id)->first();
            $quiz_question = QuizQuestion::where('quiz_id', $quiz_id->id)->orderBy('id', 'desc')->first();
        }

        $question = QuizQuestion::create(
            [
                'quiz_id' => $data['quiz_id'],
                'locale' => $data['locale'],
                'title' => $data['title'],
                'type' => $data['type'],
                'audio_media_id' => $audoMedia->id,
                'question_media_id' => $questionMedia->id,
                'answer' => $data['answer'],
            ]
        );

        $question->question_reference = $question->id;
        if ($data['locale'] != 'en' or $data['locale'] != 'En') {
            $question->question_reference = $quiz_question->question_reference;
        }
        $question->save();

        $response = [
            'data' => $question->id,
            'message' => "Question created successfully",
        ];
        return new JsonResponse($response, Response::HTTP_OK);
    }

    public function createQuestionOption(Request $request) : JsonResponse
    {
        $data = $request->validate(
			[
				'question_id' => 'required|integer',
                'locale'    => 'required|string',
                'option_title'    => 'required|string',
                'option_image'  => 'required',
			]
		);
        $media = $data['option_image'];

        $mediaName = $media->getClientOriginalName();
		$mediaPath = Storage::put('media', $media);
        $media = Media::create(
			[
				'name'    => $mediaName,
				'path'    => $mediaPath
			]
		);

        $option = QuestionOptions::create(
			[
				'question_id' => $data['question_id'],
                'locale' => $data['locale'],
                'option_title' => $data['option_title'],
                'option_image_id' => $media->id,
			]
		);

        $response = [
            'data' => $option->id,
            'message' => "Option created successfully",
        ];
        return new JsonResponse($response, Response::HTTP_OK);
    }

    public function addQuestion(Request $request): JsonResponse
    {
        $data = $request->validate(
			[
				'quiz_id' => 'required|integer',
                'question_type'    => 'required|string',
                'locale'    => 'required|string',
                'title'  => 'required',
                'audio' => 'required',
                'image' => 'required',
                'options.*.title' => 'required',
                'options.*.image' => 'required',
                'options.*.check' => 'required',
			]
		);
        $audioFile = $data['audio'];

        $mediaName = $audioFile->getClientOriginalName();
		$mediaPath = Storage::put('media', $audioFile );
        $audoMedia = Media::create(
			[
				'name'    => $mediaName,
				'path'    => $mediaPath
			]
		);

        $questionImg = $data['image'];

        $qMediaName = $questionImg->getClientOriginalName();
		$qMediaPath = Storage::put('media', $questionImg );
        $questionMedia = Media::create(
			[
				'name'    => $qMediaName,
				'path'    => $qMediaPath
			]
		);

        $question = QuizQuestion::create(
			[
				'quiz_id' => $data['quiz_id'],
                'locale' => $data['locale'],
                'title' => $data['title'],
				'type'  => $data['question_type'],
                'audio_media_id' => $audoMedia->id,
                'question_media_id' => $questionMedia->id,
                'answer' => null,
			]
		);
        $questId = $question->id;
        foreach($data['options'] as $opt){
            $media = $opt['image'];

            $mediaName = $media->getClientOriginalName();
            $mediaPath = Storage::put('media', $media );
            $media = Media::create(
                [
                    'name'    => $mediaName,
                    'path'    => $mediaPath
                ]
            );
            $option = QuestionOptions::create(
                [
                    'question_id' => $questId,
                    'option_title' => $opt['title'],
                    'locale' => $data['locale'],
                    'option_image_id' => $media->id,
                ]
            );
            if($opt['check']){
                QuizQuestion::where(["id" => $questId])->update(["answer" => $option->id]);
            }
        }

        $response = [
            'data' => $questId,
            'message' => "Question created successfully",
        ];
        return new JsonResponse($response, Response::HTTP_OK);
    }


    public function deleteSubject($id) : JsonResponse
    {
        Subjects::FindOrFail($id)->delete();
        return new JsonResponse(['message' => "Subject deleted"],Response::HTTP_OK);
    }

    public function deleteLevel($id) : JsonResponse
    {
        Levels::FindOrFail($id)->delete();
        return new JsonResponse(['message' => "Level deleted"],Response::HTTP_OK);
    }

    public function deleteTopic($id) : JsonResponse
    {
        $topic = Topics::FindOrFail($id)->delete();
        return new JsonResponse(['message' => "Topic deleted"],Response::HTTP_OK);
    }

    public function deleteMovie($id): JsonResponse{
        $movie = Movie::FindOrFail($id)->delete();
        return new JsonResponse(['message' => "Movie deleted"],Response::HTTP_OK);
    }
    public function deleteQuiz($id): JsonResponse
    {
        $quiz = Quiz::FindOrFail($id);
        $quiz->quizQuestions->each(function($questions) {
            $questions->options()->delete();
        });
        $quiz->quizQuestions()->delete();
        $quiz->delete();

        return new JsonResponse(['message' => "Quiz deleted"],Response::HTTP_OK);
    }

    public function deleteQuestion($id): JsonResponse
    {
        $question = QuizQuestion::FindOrFail($id);
        $question->options()->delete();
        $question->delete();
        return new JsonResponse(['message' => "Question deleted"],Response::HTTP_OK);
    }


    public function updateQuestion(Request $request): JsonResponse
    {
        $data = $request->validate(
            [
                'question_id' => 'required|integer',
                'title' => 'string',
                'question_type' => 'string',
                'locale' => 'string',
                'audio' => 'nullable',
                'image' => 'nullable',
                'options.*.id' => 'nullable',
                'options.*.title' => 'nullable',
                'options.*.image' => 'nullable',
                'options.*.check' => 'nullable',
            ]
        );
        // Update Question Information
        QuizQuestion::where(['id' => $data["question_id"]])->update([
            'title' => $data['title'],
            'type' => $data['question_type'],
        ]);
        if ($request->hasFile('audio')) {
            $media = $this->upload($data['audio']);
            QuizQuestion::where(['id' => $data["question_id"]])->update([
                'audio_media_id' => $media->id,
            ]);
        }
        if ($request->hasFile('image')) {
            $media = $this->upload($data['image']);
            QuizQuestion::where(['id' => $data["question_id"]])->update([
                'question_media_id' => $media->id,
            ]);
        }
        DB::transaction(function () use ($data) {
            // Edit option value
            if (isset($data['options'])) {
                $oldOptionIds = QuestionOptions::where('question_id', $data["question_id"])->pluck('id');
                $newOptionIds = [];
                foreach ($data['options'] as $opt) {
                    if (isset($opt['id'])) {
                        $option = QuestionOptions::where(['id' => $opt['id']])->update([
                            'option_title' => $opt['title'],
                        ]);
                        $op_id = $opt['id'];
                        $newOptionIds[] = $op_id;
                        if (isset($opt['image'])) {
                            $media = $this->upload($opt['image']);
                            QuestionOptions::where(['id' => $op_id])->update([
                                'option_image_id' => $media->id,
                            ]);
                        }
                    } else {
                        $media = $this->upload($opt['image']);
                        $option = QuestionOptions::create(
                            [
                                'question_id' => $data["question_id"],
                                'option_title' => $opt['title'],
                                'locale' => $data['locale'],
                                'option_image_id' => $media->id,
                            ]
                        );
                        $op_id = $option->id;

                        $newOptionIds[] = $op_id;
                    }
                    if ($opt['check']) {
                        QuizQuestion::where(["id" => $data["question_id"]])->update(["answer" => $op_id]);
                    }
                }
                $deleted = $oldOptionIds->diff(collect($newOptionIds));
                QuestionOptions::whereIn('id', $deleted)->delete();
            }
        });
        $response = [
            'message' => "Question update successfully",
        ];
        return new JsonResponse($response, Response::HTTP_OK);
    }

    public function createSubjects(Request $request) : JsonResponse
    {
        $data = $request->validate(
			[
                'flag'  => 'integer|nullable',
                'subject.*.title' => 'required',
                'subject.*.locale' => 'required|string'
			]
		);
        $subject = Subjects::create(
			[
				'flag' => 0,
			]
		);
        $data['subject_id'] = $subject->id;
        DB::transaction(function () use ($data) {
            foreach($data['subject'] as $subj){

                $subjects = SubjectTranslation::create(
                    [
                        'subject_id' => $data['subject_id'],
                        'title' => $subj['title'],
                        'locale' => $subj['locale'],
                    ]
                );
            }
        });
        $response = [
            'data'      => $subject->id,
            'message'   => "Subject created successfully",
        ];
        return new JsonResponse($response, Response::HTTP_OK);
    }
    public function updateSubjects(Request $request) : JsonResponse
    {
        $data = $request->validate(
			[
                'subject_id'  => 'integer|required',
                'subject.*.title' => 'required',
                'subject.*.locale' => 'required|string'
			]
		);
        DB::transaction(function () use ($data) {
            foreach($data['subject'] as $subj){

                 SubjectTranslation::updateOrCreate(
                    [
                        'subject_id' => $data['subject_id'],
                        'locale' => $subj['locale']
                    ],
                    [
                        'locale' => $subj['locale'],
                        'title' => $subj['title'],
                    ]
                );
            }
        });
        $response = [
            'message' => "Subject update successfully",
        ];
        return new JsonResponse($response, Response::HTTP_OK);
    }

    public function createLevels(Request $request) : JsonResponse
    {
        $data = $request->validate(
			[
				'subject_id'        => 'required|integer',
                'flag'              => 'integer|nullable',
                'level'             => 'required|integer',
                'total_rewards'     => 'string|required',
                'total_question'    => 'string|required',
                'levels.*.description' => 'string|required',
                'levels.*.locale' => 'required|string'
			]
		);
        $level = Levels::create($data);
        $data['level_id'] = $level->id;
        DB::transaction(function () use ($data) {
            foreach($data['levels'] as $lvl){

                $lvlData = LevelTranslation::create(
                    [
                        'level_id' => $data['level_id'],
                        'description' => $lvl['description'],
                        'locale' => $lvl['locale'],
                    ]
                );
            }
        });
        $response = [
            'data' => $level->id,
            'message' => "Level created successfully",
        ];
        return new JsonResponse($response, Response::HTTP_OK);
    }

    public function updateLevels(Request $request) : JsonResponse
    {

        $data = $request->validate(
			[
                'level_id'  => 'integer|required',
                'level'             => 'required|integer',
                'total_rewards'     => 'string|required',
                'total_question'    => 'string|required',
                'levels.*.description' => 'string|required',
                'levels.*.locale' => 'required|string'
			]
		);
        Levels::updateOrCreate(['id' => $data["level_id"]], $data);
        DB::transaction(function () use ($data) {
            foreach($data['levels'] as $lvl){

                LevelTranslation::updateOrCreate(
                    [
                        'level_id' => $data['level_id'],
                        'locale' => $lvl['locale']
                    ],
                    [
                        'locale' => $lvl['locale'],
                        'description' => $lvl['description'],
                    ]
                );
            }
        });
        $response = [
            'message' => "Level update successfully",
        ];
        return new JsonResponse($response, Response::HTTP_OK);
    }

    public function createTopics(Request $request) : JsonResponse
    {
        $data = $request->validate(
			[
                'level_id'      => 'required|integer',
                'flag'  => 'integer|nullable',
                'image'         => 'required|mimes:jpeg,png,jpg',
                'topic.*.title' => 'required|string',
                'topic.*.locale' => 'required|string',
                'topic.*.description' => 'required|string'
			]
		);
        $media = $this->upload($data['image']);
        $data['topic_media_id'] = $media->id;
        $topic = Topics::create($data);
        $data['topic_id'] = $topic->id;
        DB::transaction(function () use ($data) {
            foreach($data['topic'] as $tpc){

                $topics = TopicTranslation::create(
                    [
                        'topic_id' => $data['topic_id'],
                        'title' => $tpc['title'],
                        'locale' => $tpc['locale'],
                        'description' => $tpc['description'],
                    ]
                );
            }
        });
        $response = [
            'data' => $topic->id,
            'message'   => "Topic created successfully",
        ];
        return new JsonResponse($response, Response::HTTP_OK);
    }

    public function updateTopics(Request $request) : JsonResponse
    {
        $data = $request->validate(
            [
                'topic_id'      => 'required|integer',
                'image'         => 'required|mimes:jpeg,png,jpg',
                'topic.*.title' => 'required',
                'topic.*.locale' => 'required|string',
                'topic.*.description' => 'required|string'
            ]
        );
        //Topics::updateOrCreate(['id' => $data["topic_id"]], $data);
        if($request->hasFile('image')){
            $media = $this->upload($data['image']);
            Topics::where(['id' => $data["topic_id"]])->update([
                'topic_media_id'  => $media->id,
            ]);
        }
        DB::transaction(function () use ($data) {
            foreach($data['topic'] as $tpc){

                TopicTranslation::updateOrCreate(
                    [
                        'topic_id' => $data['topic_id'],
                        'locale' => $tpc['locale']
                    ],
                    [
                        'locale' => $tpc['locale'],
                        'title' => $tpc['title'],
                        'description' => $tpc['description'],
                    ]
                );
            }
        });
        $response = [
            'message' => "Topic update successfully",
        ];
        return new JsonResponse($response, Response::HTTP_OK);

    }


}
