<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusEnum;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Models\LaGrade;
use App\Models\LaSection;
use App\Models\LaSubject;
use App\Models\LaTeacherGrade;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Api\V1\LocationController;

class LaTeacherController extends LocationController
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
        $teachers = User::where('type', UserType::Teacher)->orderBy('id', 'desc');

        if($request->school_code){
            $teachers = $teachers->whereHas('school', function ($query) use ($request){
                $query->where('code', $request->school_code);
            });
        }

        if($request->is_life_lab != null){
            $teachers = $teachers->whereHas('school', function ($query) use ($request){
                $query->where('is_life_lab', $request->is_life_lab);
            });
        }

        if ($request->state) {
            $teachers = $teachers->where('state', $request->state);
        }

        if ($request->city) {
            $teachers = $teachers->where('city', $request->city);
        }

        $teachers = $teachers->paginate(25);

        $statesData = $this->states('india');
        $states = $statesData->getData();
        $cities = [];
        if ($request->state) {
            $cities = $this->cities($request->state)->getData();
        }
        return view('pages.admin.teachers.index', compact('teachers', 'states', 'cities', 'request'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $laGrades = LaGrade::where('status', StatusEnum::ACTIVE)->get();
        $laSections = LaSection::get();
        $laSubjects = LaSubject::where('status', StatusEnum::ACTIVE)->get();
        $schools = School::where('is_life_lab', StatusEnum::ACTIVE)->get();
        return view('pages.admin.teachers.create', compact('laGrades', 'laSections', 'laSubjects', 'schools'));
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
            'school_code' => ['required', 'exists:schools,code'],
        ]);
        $school = School::where('code', $request->school_code)->first();
        $data = $request->all();
        $data['type'] = UserType::Teacher;
        $data['created_by'] = Auth::user()->id;
        $data['school_id'] = $school->id;
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            $user = User::create($data);
        }
        foreach ($request->teacher_grades as $cn) {
            LaTeacherGrade::firstOrCreate([
                "user_id" => $user->id,
                "la_grade_id" => $cn['grades'],
                "la_section_id" => $cn['sections'],
                "la_subject_id" => $cn['subjects'],
            ]);
        }
        return redirect()->route('admin.teachers.index')->with('success', 'Teacher Created');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $laGrades = LaGrade::where('status', StatusEnum::ACTIVE)->get();
        $laSections = LaSection::get();
        $laSubjects = LaSubject::where('status', StatusEnum::ACTIVE)->get();
        $schools = School::where('is_life_lab', StatusEnum::ACTIVE)->get();
        return view('pages.admin.teachers.edit', compact('user', 'laGrades', 'laSections', 'laSubjects', 'schools'));
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
            'school_code' => ['required', 'exists:schools,code'],
        ]);
        
        $school = School::where('code', $request->school_code)->first();
        $data = $request->except(['_method', '_token']);
        $data['school_id'] = $school->id;
        $user->update($data);
        $user->laTeacherGrades()->delete();
        foreach ($request->teacher_grades as $cn) {
            LaTeacherGrade::firstOrCreate([
                "user_id" => $user->id,
                "la_grade_id" => $cn['grades'],
                "la_section_id" => $cn['sections'],
                "la_subject_id" => $cn['subjects'],
            ]);
        }
        return redirect()->route('admin.teachers.index')->with('success', 'Teacher Updated');
    }
}
