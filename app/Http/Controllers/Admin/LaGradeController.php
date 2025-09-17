<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Models\LaGrade;
use Illuminate\Http\Request;
class LaGradeController extends Controller
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
        $laGrades = LaGrade::orderBy('id', 'desc')->paginate(25);
        return view('pages.admin.grades.index', compact('laGrades'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.admin.grades.create');
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
        LaGrade::firstOrcreate($data);
        return redirect()->route('admin.grades.index')->with('success', 'Grade Created');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(LaGrade $laGrade)
    {
        return view('pages.admin.grades.edit', compact('laGrade'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LaGrade $laGrade)
    {
        $data = $request->except(['_method', '_token']);
        LaGrade::find($laGrade->id)->update($data);
        return redirect()->route('admin.grades.index')->with('success', 'Grade Updated');
    }

    public function statusChange(LaGrade $laGrade)
    {
        if ($laGrade->status == StatusEnum::ACTIVE) {
            $laGrade->status = StatusEnum::DEACTIVE;
        } else {
            $laGrade->status =  StatusEnum::ACTIVE;
        }
        $laGrade->save();
        return response()->json(["status" => 200, "message" => "Grade Status Changed"]);
    }
}
