<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Traits\MediaUpload;
use App\Models\LaLevel;
use App\Models\Language;
use App\Models\LaSubject;
use App\Models\LaTopic;
use Illuminate\Http\Request;

class LaTopicController extends Controller
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
        $laTopics = LaTopic::orderBy('id', 'desc');

        if ($request->type) {
            $laTopics->where('type', $request->type);
        }

        if ($request->la_level_id) {
            $laTopics->where('la_level_id', $request->la_level_id);
        }

        if ($request->la_subject_id) {
            $laTopics->where('la_subject_id', $request->la_subject_id);
        }

        $laTopics = $laTopics->get();
        return view('pages.admin.topics.index', compact('laTopics'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $laLevels = LaLevel::where('status', StatusEnum::ACTIVE)->get();
        $laSubjects = LaSubject::where('status', StatusEnum::ACTIVE)->get();
        $languages = Language::where('status', StatusEnum::ACTIVE)->get();
        return view('pages.admin.topics.create', compact('languages', 'laSubjects', 'laLevels', 'request'));
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
        $image = [];
        foreach ($request->topic_translation as $cn) {
            $titleData[$cn['language']] = $cn['title'];
            $media = $this->upload($cn['image']);
            $image[$cn['language']] =  $media->id;
        }

        $data = $request->all();
        $data['title'] = $titleData;
        $data['image'] = $image;
        LaTopic::create($data);
        return redirect()->route('admin.topics.index')->with('success', 'Topic Created');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(LaTopic $laTopic)
    {
        $laLevels = LaLevel::where('status', StatusEnum::ACTIVE)->get();
        $laSubjects = LaSubject::where('status', StatusEnum::ACTIVE)->get();
        $languages = Language::where('status', StatusEnum::ACTIVE)->get();
        $imageBaseUrl = $this->getBaseUrl();
        return view('pages.admin.topics.edit', compact('languages', 'laTopic', 'imageBaseUrl', 'laLevels', 'laSubjects'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LaTopic $laTopic)
    {
        $data = $request->except(['_method', '_token']);
        $titleData = [];
        $image = [];
        foreach ($request->topic_translation as $cn) {
            $titleData[$cn['language']] = $cn['title'];
            $mediaId = null;
            if (isset($cn['media_id'])) {
                $mediaId = $cn['media_id'];
            }
            if (isset($cn['image'])) {
                $media = $this->upload($cn['image']);
                $mediaId = $media->id;
            }
            $image[$cn['language']] =  $mediaId;
        }
        $data['title'] = $titleData;
        $data['image'] = $image;
        LaTopic::find($laTopic->id)->update($data);
        return redirect()->route('admin.topics.index')->with('success', 'Topic Updated');
    }

    public function statusChange(LaTopic $laTopic)
    {
        if ($laTopic->status == StatusEnum::ACTIVE) {
            $laTopic->status = StatusEnum::DEACTIVE;
        } else {
            $laTopic->status =  StatusEnum::ACTIVE;
        }
        $laTopic->save();
        return response()->json(["status" => 200, "message" => "Topic Status Changed"]);
    }
}
