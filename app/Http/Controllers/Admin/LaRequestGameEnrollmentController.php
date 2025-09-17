<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LaGameEnrollment;
use App\Models\LaRequestGameEnrollment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaRequestGameEnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $laRequestGameEnrollments = LaRequestGameEnrollment::orderBy('id', 'desc');
        if ($request->type) {
            $laRequestGameEnrollments->where('type', $request->type);
        }
        if ($request->status) {
            if ($request->status == "pending") {
                $laRequestGameEnrollments->whereNull('approved_at');
            }
            if ($request->status == "approved") {
                $laRequestGameEnrollments->whereNotNull('approved_at');
            }
        }
        $laRequestGameEnrollments = $laRequestGameEnrollments->paginate(50);
        return view('pages.admin.enrollment-codes.requests', compact('laRequestGameEnrollments', 'request'));
    }

    public function approveEnrollment(LaRequestGameEnrollment $laRequestGameEnrollment)
    {
        if ($laRequestGameEnrollment->la_game_enrollment_id) {
            return response()->json(["status" => 400, "message" => "Already Approve Enrollment"]);
        }
        $checkPreviousenrollment = LaGameEnrollment::orderBy('id', 'desc')->first();

        $enrollmentCodeId = 0;
        if ($checkPreviousenrollment) {
            $enrollmentCodeId = $checkPreviousenrollment->id;
        }
        $formattedId = str_pad($enrollmentCodeId, 4, '0', STR_PAD_LEFT);
        $laGameEnrollment = LaGameEnrollment::create([
            "type" => $laRequestGameEnrollment->type,
            "enrollment_code" => $formattedId,
            "user_id" => $laRequestGameEnrollment->user_id,
            "unlock_enrollment_at" => Carbon::now()->toDateTimeString(),
        ]);

        $laRequestGameEnrollment->update([
            "la_game_enrollment_id" => $laGameEnrollment->id,
            "approved_at" => Carbon::now()->toDateTimeString(),
        ]);
        return response()->json(["status" => 200, "message" => "Game Enrollment Request Approved"]);
    }
}
