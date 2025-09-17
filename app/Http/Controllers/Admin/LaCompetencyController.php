<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Traits\MediaUpload;
use App\Models\LaCompetency;
use App\Models\LaLevel;
use App\Models\LaSubject;
use App\Models\LaTopic;
use Illuminate\Http\Request;

class LaCompetencyController extends Controller
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
        $laCompetencies = LaCompetency::orderBy('id', 'desc');
        if ($request->la_subject_id) {
            $laCompetencies->where('la_subject_id', $request->la_subject_id);
        }
        if ($request->status) {
            $laCompetencies->where('status', $request->status);
        }
        $laCompetencies = $laCompetencies->paginate(25);
        return view('pages.admin.competencies.index', compact('laCompetencies', 'subjects', 'request'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $laLevels = LaLevel::where('status', StatusEnum::ACTIVE)->get();
        $subjects = LaSubject::where('status', StatusEnum::ACTIVE)->get();
        return view('pages.admin.competencies.create', compact('subjects', 'laLevels'));
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
        LaCompetency::create($data);
        return redirect()->route('admin.competencies.index')->with('success', 'Compentency Created');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(LaCompetency $laCompetency)
    {
        $laLevels = LaLevel::where('status', StatusEnum::ACTIVE)->get();
        $subjects = LaSubject::where('status', StatusEnum::ACTIVE)->get();
        $imageBaseUrl = $this->getBaseUrl();
        return view('pages.admin.competencies.edit', compact('laCompetency', 'subjects', 'imageBaseUrl', 'laLevels'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LaCompetency $laCompetency)
    {
        $data = $request->except(['_method', '_token']);  
        if ($request->document) {
            $media = $this->upload($request->document);
            $data['document'] =  $media->id;
        }
        LaCompetency::find($laCompetency->id)->update($data);
        return redirect()->route('admin.competencies.index')->with('success', 'Compentency Updated');
    }

    public function statusChange(LaCompetency $laCompetency)
    {
        if ($laCompetency->status == StatusEnum::ACTIVE) {
            $laCompetency->status = StatusEnum::DEACTIVE;
        } else {
            $laCompetency->status =  StatusEnum::ACTIVE;
        }
        $laCompetency->update();
        return response()->json(["status" => 200, "message" => "Competency Status Changed"]);
    }
}
