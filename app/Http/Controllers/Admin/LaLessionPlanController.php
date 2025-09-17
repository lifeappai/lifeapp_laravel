<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Traits\MediaUpload;
use App\Models\LaBoard;
use App\Models\LaLessionPlan;
use App\Models\LaLessionPlanLanguage;
use Illuminate\Http\Request;

class LaLessionPlanController extends Controller
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
        $laLessionPlans = LaLessionPlan::orderBy('id', 'desc');
        $laLessionPlanLanguages = LaLessionPlanLanguage::where('status', StatusEnum::ACTIVE)->get();
        if ($request->status) {
            $laLessionPlans->where('status', $request->status);
        }
        if ($request->la_lession_plan_language_id) {
            $laLessionPlans->where('la_lession_plan_language_id', $request->la_lession_plan_language_id);
        }
        if ($request->title) {
            $laLessionPlans->where('title', $request->title);
        }
        $laLessionPlans = $laLessionPlans->paginate(25);
        $imageBaseUrl = $this->getBaseUrl();
        return view('pages.admin.lession-plans.index', compact('laLessionPlans', 'request', 'imageBaseUrl', 'laLessionPlanLanguages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $laBoards = LaBoard::where('status', StatusEnum::ACTIVE)->get();
        $laLessionPlanLanguages = LaLessionPlanLanguage::where('status', StatusEnum::ACTIVE)->get();
        return view('pages.admin.lession-plans.create', compact('laBoards', 'laLessionPlanLanguages'));
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
        LaLessionPlan::create($data);
        return redirect()->route('admin.lession.plans.index')->with('success', 'Lession Plan Created');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(LaLessionPlan $laLessionPlan)
    {
        $laBoards = LaBoard::where('status', StatusEnum::ACTIVE)->get();
        $laLessionPlanLanguages = LaLessionPlanLanguage::where('status', StatusEnum::ACTIVE)->get();
        $imageBaseUrl = $this->getBaseUrl();
        return view('pages.admin.lession-plans.edit', compact('laLessionPlan', 'laLessionPlanLanguages', 'imageBaseUrl', 'laBoards'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LaLessionPlan $laLessionPlan)
    {
        $data = $request->except(['_method', '_token']);
        if ($request->document) {
            $media = $this->upload($request->document);
            $data['document'] =  $media->id;
        }
        LaLessionPlan::find($laLessionPlan->id)->update($data);
        return redirect()->route('admin.lession.plans.index')->with('success', 'Lession Plan Updated');
    }

    public function statusChange(LaLessionPlan $laLessionPlan)
    {
        if ($laLessionPlan->status == StatusEnum::ACTIVE) {
            $laLessionPlan->status = StatusEnum::DEACTIVE;
        } else {
            $laLessionPlan->status =  StatusEnum::ACTIVE;
        }
        $laLessionPlan->update();
        return response()->json(["status" => 200, "message" => "Lession Plan Status Changed"]);
    }
}