<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Models\LaLevel;
use App\Models\Language;
use Illuminate\Http\Request;

class LaLevelController extends Controller
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
        $levels = LaLevel::orderBy('id', 'desc')->paginate(25);
        return view('pages.admin.levels.index', compact('levels'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $languages = Language::where('status', StatusEnum::ACTIVE)->get();
        return view('pages.admin.levels.create', compact('languages'));
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
        $descriptionData = [];
        foreach ($request->level_translation as $cn) {
            $titleData[$cn['language']] = $cn['title'];
            $descriptionData[$cn['language']] = $cn['description'];
        }

        $data = $request->all();
        $data['title'] = $titleData;
        $data['description'] = $descriptionData;
        LaLevel::create($data);
        return redirect()->route('admin.levels.index')->with('success', 'Level Created');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(LaLevel $laLevel)
    {
        $languages = Language::where('status', StatusEnum::ACTIVE)->get();
        return view('pages.admin.levels.edit', compact('languages', 'laLevel'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LaLevel $laLevel)
    {
        $data = $request->except(['_method', '_token']);
        $titleData = [];
        $descriptionData = [];
        foreach ($request->level_translation as $cn) {
            $titleData[$cn['language']] = $cn['title'];
            $descriptionData[$cn['language']] = $cn['description'];
        }
        $data['title'] = $titleData;
        $data['description'] = $descriptionData;
        LaLevel::find($laLevel->id)->update($data);
        return redirect()->route('admin.levels.index')->with('success', 'Level Updated');
    }

    public function statusChange(LaLevel $laLevel)
    {
        if ($laLevel->status == StatusEnum::ACTIVE) {
            $laLevel->status = StatusEnum::DEACTIVE;
        } else {
            $laLevel->status =  StatusEnum::ACTIVE;
        }
        $laLevel->save();
        return response()->json(["status" => 200, "message" => "Level Status Changed"]);
    }
}
