<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Traits\MediaUpload;
use App\Models\LaAssessment;
use App\Models\LaGrade;
use App\Models\LaSubject;
use App\Models\LaTopic;
use Illuminate\Http\Request;

class LaAssessmentController extends Controller
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
        $subjects = LaSubject::where('status', StatusEnum::ACTIVE)->get();
        $laAssessments = LaAssessment::orderBy('id', 'desc');
        if ($request->la_subject_id) {
            $laAssessments->where('la_subject_id', $request->la_subject_id);
        }
        if ($request->status) {
            $laAssessments->where('status', $request->status);
        }
        $laAssessments = $laAssessments->paginate(25);
        $imageBaseUrl = $this->getBaseUrl();
        return view('pages.admin.assessments.index', compact('laAssessments', 'subjects', 'request', 'imageBaseUrl'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $subjects = LaSubject::where('status', StatusEnum::ACTIVE)->get();
        $laGrades = LaGrade::orderBy('name', 'desc')->where('status', StatusEnum::ACTIVE)->get();
        return view('pages.admin.assessments.create', compact('subjects',  'laGrades'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $media = $this->upload($request->document);
        $data['document'] =  $media->id;
        LaAssessment::create($data);
        return redirect()->route('admin.assessments.index')->with('success', 'Assessment Created');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(LaAssessment $laAssessment)
    {
        $subjects = LaSubject::where('status', StatusEnum::ACTIVE)->get();
        $laGrades = LaGrade::orderBy('name', 'desc')->where('status', StatusEnum::ACTIVE)->get();
        $imageBaseUrl = $this->getBaseUrl();
        return view('pages.admin.assessments.edit', compact('laAssessment', 'subjects', 'imageBaseUrl', 'laGrades'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LaAssessment $laAssessment)
    {
        $data = $request->except(['_method', '_token']);
        if ($request->document) {
            $media = $this->upload($request->document);
            $data['document'] =  $media->id;
        }
        LaAssessment::find($laAssessment->id)->update($data);
        return redirect()->route('admin.assessments.index')->with('success', 'Assessment Updated');
    }

    public function statusChange(LaAssessment $laAssessment)
    {
        if ($laAssessment->status == StatusEnum::ACTIVE) {
            $laAssessment->status = StatusEnum::DEACTIVE;
        } else {
            $laAssessment->status =  StatusEnum::ACTIVE;
        }
        $laAssessment->update();
        return response()->json(["status" => 200, "message" => "Assessment Status Changed"]);
    }
}
