<?php

namespace App\Http\Controllers\Admin;

use App\Enums\LanguageEnum;
use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LanguageController extends Controller
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
        $languages = Language::orderBy('title')->paginate(25);
        return view('pages.admin.languages.index', compact('languages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $languages = LanguageEnum::LANGUAGES;
        return view('pages.admin.languages.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'slug' => ['required', Rule::unique('languages')],
        ]);


        $data = $request->all();
        $data['title'] = LanguageEnum::LANGUAGES[$request->slug];
        Language::create($data);
        return redirect()->route('admin.languages.index')->with('success', 'Language Created');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Language $language)
    {
        $languages = LanguageEnum::LANGUAGES;
        return view('pages.admin.languages.edit', compact('language', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Language $language)
    {
        $request->validate([
            'slug' => ['required', Rule::unique('languages')->ignore($language->id)],
        ]);
        $data = $request->except(['_method', '_token']);
        $data['title'] = LanguageEnum::LANGUAGES[$request->slug];
        Language::find($language->id)->update($data);
        return redirect()->route('admin.languages.index')->with('success', 'Language Updated');
    }

    public function statusChange(Language $language)
    {
        if ($language->status == StatusEnum::ACTIVE) {
            $language->status = StatusEnum::DEACTIVE;
        } else {
            $language->status =  StatusEnum::ACTIVE;
        }
        $language->save();
        return response()->json(["status" => 200, "message" => "Language Status Changed"]);
    }
}
