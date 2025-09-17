<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Models\LaLessionPlanLanguage;
use Illuminate\Http\Request;

class LaLessionPlanLanguageController extends Controller
{
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
        $laLessionPlanLanguages = LaLessionPlanLanguage::orderBy('id', 'desc')->paginate(25);
        return view('pages.admin.lession-plan-languages.index', compact('laLessionPlanLanguages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.admin.lession-plan-languages.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->except(['_method', '_token']);
        LaLessionPlanLanguage::firstOrcreate($data);
        return redirect()->route('admin.lession.plan.languages.index')->with('success', 'Lession Plan Language Created');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(LaLessionPlanLanguage $laLessionPlanLanguage)
    {
        return view('pages.admin.lession-plan-languages.edit', compact('laLessionPlanLanguage'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LaLessionPlanLanguage $laLessionPlanLanguage)
    {
        $data = $request->except(['_method', '_token']);
        LaLessionPlanLanguage::find($laLessionPlanLanguage->id)->update($data);
        return redirect()->route('admin.lession.plan.languages.index')->with('success', 'Lession Plan Language Updated');
    }

    public function statusChange(LaLessionPlanLanguage $laLessionPlanLanguage)
    {
        if ($laLessionPlanLanguage->status == StatusEnum::ACTIVE) {
            $laLessionPlanLanguage->status = StatusEnum::DEACTIVE;
        } else {
            $laLessionPlanLanguage->status =  StatusEnum::ACTIVE;
        }
        $laLessionPlanLanguage->save();
        return response()->json(["status" => 200, "message" => "Lession Plan Language Status Changed"]);
    }
}
