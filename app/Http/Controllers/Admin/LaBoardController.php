<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Models\LaBoard;
use Illuminate\Http\Request;

class LaBoardController extends Controller
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
        $laBoards = LaBoard::orderBy('id', 'desc')->get();
        return view('pages.admin.boards.index', compact('laBoards'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.admin.boards.create');
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
        LaBoard::create($data);
        return redirect()->route('admin.boards.index')->with('success', 'Board Created');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(LaBoard $laBoard)
    {
        return view('pages.admin.boards.edit', compact('laBoard'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LaBoard $laBoard)
    {
        $data = $request->except(['_method', '_token']);
        LaBoard::find($laBoard->id)->update($data);
        return redirect()->route('admin.boards.index')->with('success', 'Board Updated');
    }

    public function statusChange(LaBoard $laBoard)
    {
        if ($laBoard->status == StatusEnum::ACTIVE) {
            $laBoard->status = StatusEnum::DEACTIVE;
        } else {
            $laBoard->status =  StatusEnum::ACTIVE;
        }
        $laBoard->save();
        return response()->json(["status" => 200, "message" => "Board Status Changed"]);
    }
}
