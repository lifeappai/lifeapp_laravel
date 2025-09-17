<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class MentorController extends Controller
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
        $mentors = User::where('type', UserType::Mentor)->orderBy('id', 'desc');
        $mentors = $mentors->paginate(50);
        return view('pages.admin.mentors.index', compact('mentors'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.admin.mentors.create');
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
            'email' => ['required', Rule::unique('users')],
            'mobile_no' => ['required', Rule::unique('users')],
            'pin' => ['required', Rule::unique('users')],
        ]);
        $data = $request->all();
        $data['type'] = UserType::Mentor;
        User::create($data);
        return redirect()->route('admin.mentors.index')->with('success', 'Mentor Created');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('pages.admin.mentors.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'email' => ['required', Rule::unique('users')->ignore($user->id)],
            'mobile_no' => ['required', Rule::unique('users')->ignore($user->id)],
            'pin' => ['required', Rule::unique('users')->ignore($user->id)],
        ]);
        $data = $request->except(['_method', '_token']);
        User::find($user->id)->update($data);
        return redirect()->route('admin.mentors.index')->with('success', 'Mentor Updated');
    }
}
