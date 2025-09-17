<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Models\LaSection;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LaSectionController extends Controller
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
        $status = $request->status;
        $laSections = LaSection::orderBy('id', 'desc');
        if ($status != "") {
            $laSections = $laSections->where('status', $status);
        }
        $laSections = $laSections->paginate(50);
        return view('pages.admin.sections.index', compact('laSections', 'status'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.admin.sections.create');
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
            'name' => ['required', Rule::unique('la_sections')],
        ]);
        $data = $request->all();
        LaSection::create($data);
        return redirect()->route('admin.sections.index')->with('success', 'Section Created');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(LaSection $laSection)
    {
        return view('pages.admin.sections.edit', compact('laSection'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LaSection $laSection)
    {
        $request->validate([
            'name' => ['required', Rule::unique('la_sections')->ignore($laSection->id)],
        ]);
        $data = $request->except(['_method', '_token']);
        LaSection::find($laSection->id)->update($data);
        return redirect()->route('admin.sections.index')->with('success', 'Section Updated');
    }

    public function statusChange(LaSection $laSection)
    {
        if ($laSection->status == StatusEnum::ACTIVE) {
            $laSection->status = StatusEnum::DEACTIVE;
        } else {
            $laSection->status =  StatusEnum::ACTIVE;
        }
        $laSection->save();
        return response()->json(["status" => 200, "message" => "LaSection Status Changed"]);
    }
}
