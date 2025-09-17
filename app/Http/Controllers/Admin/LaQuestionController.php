<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusEnum;
use App\Enums\SubjectEnum;
use App\Http\Controllers\Controller;
use App\Http\Traits\MediaUpload;
use App\Models\LaLevel;
use App\Models\Language;
use App\Models\LaQuestion;
use App\Models\LaQuestionOption;
use App\Models\LaSubject;
use App\Models\LaTopic;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use MigrationsGenerator\DBAL\Types\LongTextType;

class LaQuestionController extends Controller
{
    use MediaUpload;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $levels = LaLevel::where('status', StatusEnum::ACTIVE)->get();
        $subjects = LaSubject::where('status', StatusEnum::ACTIVE)->get();
        $laTopics = LaTopic::where('status', StatusEnum::ACTIVE)->get();
        $questions = LaQuestion::orderBy('index');
        $levelId = $request->la_level_id;
        $subjectId = $request->la_subject_id;
        $topicId = $request->la_topic_id;
        $type = $request->type;
        $statusId = $request->status;
        if ($levelId) {
            $questions->where('la_level_id', $levelId);
        }
        if ($subjectId) {
            $questions->where('la_subject_id', $subjectId);
        }
        if ($topicId) {
            $questions->where('la_topic_id', $topicId);
        }
        if ($type) {
            $questions->where('type', $type);
        }
        if (isset($statusId)) {
            $questions->where('status', $statusId);
        }
        $imageBaseUrl = $this->getBaseUrl();
        $questions = $questions->paginate(25);
        return view('pages.admin.questions.index', compact('questions', 'levels', 'levelId', 'subjectId', 'subjects', 'statusId', 'type', 'imageBaseUrl', 'topicId', 'laTopics'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $levels = LaLevel::where('status', StatusEnum::ACTIVE)->get();
        $subjects = LaSubject::where('status', StatusEnum::ACTIVE)->get();
        $languages = Language::where('status', StatusEnum::ACTIVE)->get();
        $laTopics = LaTopic::orderBy('id', 'desc')->where('status', StatusEnum::ACTIVE)->get();
        return view('pages.admin.questions.create', compact('subjects', 'languages', 'levels', 'laTopics', 'request'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $titleData = [];
        foreach ($request->question_translations as $key => $cn) {
            if ($cn['title'] instanceof UploadedFile) {
                $titleMedia = $this->upload($cn['title']);
                $titleData[$cn['language']] = $titleMedia->id;
            } else {
                $titleData[$cn['language']] = $cn['title'];
            }
        }
        $data = $request->all();
        $data['title'] = $titleData;
        $data['created_by'] = Auth::user()->id;
        LaQuestion::create($data);
        $inputs = $request->except('_token', 'question_type', 'question_translations');
        return redirect()->route('admin.questions.index', $inputs)->with('success', 'Question Created');
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(LaQuestion $laQuestion)
    {
        $levels = LaLevel::where('status', StatusEnum::ACTIVE)->get();
        $subjects = LaSubject::where('status', StatusEnum::ACTIVE)->get();
        $languages = Language::where('status', StatusEnum::ACTIVE)->get();
        $laTopics = LaTopic::orderBy('id', 'desc')->where('status', StatusEnum::ACTIVE)->get();
        $imageBaseUrl = $this->getBaseUrl();
        return view('pages.admin.questions.edit', compact('languages', 'subjects', 'levels', 'laQuestion', 'laTopics', 'imageBaseUrl'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LaQuestion $laQuestion)
    {
        $data = $request->except(['_method', '_token']);
        $titleData = [];
        foreach ($request->question_translations as $key => $cn) {
            if (isset($cn['media_id'])) {
                $titleData[$cn['language']] = $cn['media_id'];
            }

            if (isset($cn['title'])) {
                if ($cn['title'] instanceof UploadedFile) {
                    $titleMedia = $this->upload($cn['title']);
                    $titleData[$cn['language']] = $titleMedia->id;
                } else {
                    $titleData[$cn['language']] = $cn['title'];
                }
            }
        }
        $data['title'] = $titleData;
        $data['created_by'] = Auth::user()->id;
        LaQuestion::find($laQuestion->id)->update($data);
        $inputs = $request->except('_token', 'question_type', 'question_translations');
        return redirect()->route('admin.questions.index', $inputs)->with('success', 'Question Updated');
    }

    public function editAnswers(LaQuestion $laQuestion)
    {
        $answers = LaQuestionOption::where('question_id', $laQuestion->id)->get();
        $languages = Language::where('status', StatusEnum::ACTIVE)->get();
        return view('pages.admin.questions.answers', compact('laQuestion', 'languages', 'answers'));
    }

    public function updateAnswers(LaQuestion $laQuestion, Request $request)
    {
        if ($laQuestion->answer_option_id) {
            $laQuestion->answer_option_id = null;
            $laQuestion->save();
        }
        $laQuestion->questionOptions()->delete();
        foreach ($request->new_option as $cn) {
            $titleData = [];
            foreach ($cn['option'] as $option) {
                $titleData[$option['language']] = $option['title'];
            }
            $answerData = [
                "title" => $titleData,
                "question_id" => $laQuestion->id,
            ];
            $answerOption = LaQuestionOption::create($answerData);
            if (isset($cn['correct_answer'])) {
                $laQuestion->answer_option_id = $answerOption->id;
                $laQuestion->save();
            }
        }
        return redirect()->route('admin.questions.index')->with('success', 'Answer Added');
    }

    public function statusChange(LaQuestion $laQuestion)
    {
        if ($laQuestion->status == StatusEnum::ACTIVE) {
            $laQuestion->status = StatusEnum::DEACTIVE;
        } else {
            $laQuestion->status =  StatusEnum::ACTIVE;
        }
        $laQuestion->save();
        return response()->json(["status" => 200, "message" => "Question Status Changed"]);
    }

    public function indexChange(LaQuestion $laQuestion, Request $request)
    {
        $index = $request->index;
        if (!$index) {
            $index = 1;
        }
        $laQuestion->index = $index;
        $laQuestion->update();
        return response()->json(["status" => 200, "message" => "Question Index Changed", "index" => $index]);
    }
}
