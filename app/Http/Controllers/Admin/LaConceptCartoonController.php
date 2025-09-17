<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Traits\MediaUpload;
use App\Models\LaConceptCartoon;
use App\Models\LaConceptCartoonHeader;
use App\Models\LaLevel;
use App\Models\LaSubject;
use App\Models\LaTopic;
use Illuminate\Http\Request;

class LaConceptCartoonController extends Controller
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
        $laConceptCartoons = LaConceptCartoon::orderBy('id', 'desc');
        if ($request->la_subject_id) {
            $laConceptCartoons->where('la_subject_id', $request->la_subject_id);
        }
        if ($request->status) {
            $laConceptCartoons->where('status', $request->status);
        }
        $laConceptCartoons = $laConceptCartoons->paginate(25);
        return view('pages.admin.concept-cartoons.index', compact('laConceptCartoons', 'subjects', 'request'));
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
        return view('pages.admin.concept-cartoons.create', compact('subjects', 'laLevels'));
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
        LaConceptCartoon::create($data);
        return redirect()->route('admin.concept.cartoons.index')->with('success', 'Cartoon Concept Created');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(LaConceptCartoon $laCartoonConcept)
    {
        $laLevels = LaLevel::where('status', StatusEnum::ACTIVE)->get();
        $subjects = LaSubject::where('status', StatusEnum::ACTIVE)->get();
        $imageBaseUrl = $this->getBaseUrl();
        return view('pages.admin.concept-cartoons.edit', compact('laCartoonConcept', 'subjects', 'imageBaseUrl', 'laLevels'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LaConceptCartoon $laCartoonConcept)
    {
        $data = $request->except(['_method', '_token']);
        if ($request->document) {
            $media = $this->upload($request->document);
            $data['document'] =  $media->id;
        }
        LaConceptCartoon::find($laCartoonConcept->id)->update($data);
        return redirect()->route('admin.concept.cartoons.index')->with('success', 'Cartoon Concept Updated');
    }

    public function statusChange(LaConceptCartoon $laCartoonConcept)
    {
        if ($laCartoonConcept->status == StatusEnum::ACTIVE) {
            $laCartoonConcept->status = StatusEnum::DEACTIVE;
        } else {
            $laCartoonConcept->status =  StatusEnum::ACTIVE;
        }
        $laCartoonConcept->update();
        return response()->json(["status" => 200, "message" => "Concept Cartoon Status Changed"]);
    }

    public function headers()
    {
        $imageBaseUrl = $this->getBaseUrl();
        $laCartoonConceptHeader = LaConceptCartoonHeader::first();
        return view('pages.admin.concept-cartoons.headers', compact('laCartoonConceptHeader', 'imageBaseUrl'));
    }

    public function storeHeaders(Request $request)
    {
        $laCartoonConceptHeader = LaConceptCartoonHeader::first();
        $data = $request->all();
        if ($request->media) {
            $media = $this->upload($request->media);
            $data['media_id'] =  $media->id;
        }
        if ($laCartoonConceptHeader) {
            $laCartoonConceptHeader->update($data);
        } else {
            LaConceptCartoonHeader::create($data);
        }
        return redirect()->route('admin.concept.cartoons.headers')->with('success', 'Concept Cartoon Header Updated');
    }
}
