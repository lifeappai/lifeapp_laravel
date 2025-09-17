<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Traits\MediaUpload;
use App\Models\LaGrade;
use App\Models\LaSubject;
use App\Models\LaTopic;
use App\Models\LaWorkSheet;
use Illuminate\Http\Request;

class LaWorkSheetController extends Controller
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
        $laWorkSheets = LaWorkSheet::orderBy('id', 'desc');
        if ($request->la_subject_id) {
            $laWorkSheets->where('la_subject_id', $request->la_subject_id);
        }
        if ($request->status) {
            $laWorkSheets->where('status', $request->status);
        }
        $laWorkSheets = $laWorkSheets->paginate(25);
        $imageBaseUrl = $this->getBaseUrl();
        return view('pages.admin.work-sheets.index', compact('laWorkSheets', 'subjects', 'request', 'imageBaseUrl'));
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
        return view('pages.admin.work-sheets.create', compact('subjects',  'laGrades'));
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
        LaWorkSheet::create($data);
        return redirect()->route('admin.work.sheets.index')->with('success', 'Work Sheet Created');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(LaWorkSheet $laWorkSheet)
    {
        $subjects = LaSubject::where('status', StatusEnum::ACTIVE)->get();
        $laGrades = LaGrade::orderBy('name', 'desc')->where('status', StatusEnum::ACTIVE)->get();
        $imageBaseUrl = $this->getBaseUrl();
        return view('pages.admin.work-sheets.edit', compact('laWorkSheet', 'subjects', 'imageBaseUrl', 'laGrades'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LaWorkSheet $laWorkSheet)
    {
        $data = $request->except(['_method', '_token']);
        if ($request->document) {
            $media = $this->upload($request->document);
            $data['document'] =  $media->id;
        }
        LaWorkSheet::find($laWorkSheet->id)->update($data);
        return redirect()->route('admin.work.sheets.index')->with('success', 'Work Sheet Updated');
    }

    public function statusChange(LaWorkSheet $laWorkSheet)
    {
        if ($laWorkSheet->status == StatusEnum::ACTIVE) {
            $laWorkSheet->status = StatusEnum::DEACTIVE;
        } else {
            $laWorkSheet->status =  StatusEnum::ACTIVE;
        }
        $laWorkSheet->update();
        return response()->json(["status" => 200, "message" => "Work Sheet Status Changed"]);
    }
}
