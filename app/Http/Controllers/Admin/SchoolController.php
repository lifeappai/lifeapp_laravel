<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusEnum;
use App\Http\Controllers\Api\V1\LocationController;
use App\Jobs\QuestionImportJob;
use App\Jobs\SchoolImportJob;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class SchoolController extends LocationController
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
        $state = $request->state;
        $city = $request->city;
        $code = $request->code;
        $district = $request->district;
        $statesData = $this->states('india');
        $states = $statesData->getData();
        $schools = School::filterSchool($request->all());

        $cities = [];
        if ($state) {
            $cities = $this->cities($state)->getData();
        }

        $schools = $schools->paginate(25);

        return view('pages.admin.schools.index', compact('schools', 'status', 'state', 'city', 'states', 'cities', 'code','district', 'request'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $states = $this->states("India");
        $states = $states->getData();
        return view('pages.admin.schools.create', compact('states'));
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
            'name' => ['required', Rule::unique('schools')],
        ]);
        $data = $request->all();
        School::create($data);
        return redirect()->route('admin.schools.index')->with('success', 'School Created');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(School $school)
    {
        $states = $this->states("India");
        $states = $states->getData();
        $cities = [];
        if ($school->state) {
            $cities = $this->cities($school->state);
            $cities = $cities->getData();
        }
        return view('pages.admin.schools.edit', compact('school', 'states', 'cities'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, School $school)
    {
        $request->validate([
            'name' => ['required', Rule::unique('schools')->ignore($school->id)],
        ]);
        $data = $request->except(['_method', '_token']);
        School::find($school->id)->update($data);
        return redirect()->route('admin.schools.index')->with('success', 'School Updated');
    }

    public function statusChange(School $school)
    {
        if ($school->status == StatusEnum::ACTIVE) {
            $school->status = StatusEnum::DEACTIVE;
        } else {
            $school->status =  StatusEnum::ACTIVE;
        }
        $school->save();
        return response()->json(["status" => 200, "message" => "School Status Changed"]);
    }

    public function destroy(School $school)
    {
        $school->delete();
        return response()->json(["status" => 200, "message" => "School Deleted"]);
    }

    public function import(Request $request)
    {
        $file = "school_excel_sheet.xlsx";
        if (Storage::exists("csv/" . $file)) {
            Storage::delete("csv/" . $file);
        }
        $path = $request->file('school_excel_sheet')->storeAs('storage/csv', $file);
        dispatch(new SchoolImportJob($path));
        return redirect()->back()->with('success', "Schools Added Successfully");
    }
}
