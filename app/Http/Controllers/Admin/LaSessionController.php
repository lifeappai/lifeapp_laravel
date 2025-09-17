<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Jobs\SendSessionNotificationJob;
use App\Models\LaSession;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LaSessionController extends Controller
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

    public function index(Request $request)
    {
        $laSessions = LaSession::orderBy('id', 'desc');
        if($request->status != null){
            $laSessions = $laSessions->where('status', $request->status);
        }
        $laSessions = $laSessions->paginate(50);
        return view('pages.admin.mentors-session.index', compact('laSessions','request'));
    }

    public function edit(LaSession $laSession)
    {
        return view('pages.admin.mentors-session.edit', compact('laSession'));
    }


    public function update(Request $request, LaSession $laSession)
    {
        $request->validate([
            'status' => ['required', Rule::in([StatusEnum::ACTIVE, StatusEnum::DEACTIVE])],
            'heading' => ['required'],
            'description' => ['required'],
        ]);

        $data = $request->except(['_method', '_token']);
        $laSession->update($data);
        return redirect()->route('admin.la.sessions.index')->with('success', 'Session Updated');
    }

    public function statusChange(LaSession $laSession)
    {
        $newStatus = ($laSession->status == StatusEnum::ACTIVE) ? StatusEnum::DEACTIVE : StatusEnum::ACTIVE;

        $laSession->status = $newStatus;
        $laSession->save();

        if ($laSession->status == StatusEnum::ACTIVE) {
            dispatch(new SendSessionNotificationJob($laSession->id));
        }

        return response()->json(["status" => 200, "message" => "Session Status Changed"]);
    }

}