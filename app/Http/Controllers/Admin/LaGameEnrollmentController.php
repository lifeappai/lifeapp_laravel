<?php

namespace App\Http\Controllers\Admin;

use App\Enums\GameType;
use App\Http\Controllers\Controller;
use App\Models\LaGameEnrollment;
use Illuminate\Http\Request;

class LaGameEnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->type;

        $laGameEnrollments = LaGameEnrollment::inRandomOrder();
        if ($type) {
            $laGameEnrollments = $laGameEnrollments->where('type', $type);
        }
        if ($request->type_export == "export") {
            $laGameEnrollments = $laGameEnrollments->get();
            $fileName = "game-enrollments.csv";
            $headers = array(
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            );

            $columns = array(
                'Sr No.', 'Type', 'enrollment Code', 'Assign enrollment Student', 'Unlock enrollment Date Time'
            );

            $callback = function () use ($laGameEnrollments, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);
                $i = 1;
                foreach ($laGameEnrollments as $laGameEnrollment) {
                    $row['sr_no'] = $i;
                    if ($laGameEnrollment->type == GameType::JIGYASA) {
                        $row['type'] =  "Jigyasa";
                    } elseif ($laGameEnrollment->type == GameType::PRAGYA) {
                        $row['type'] =    "Pragya";
                    } else {
                        $row['type'] = "";
                    }
                    $row['enrollment_code'] = $laGameEnrollment->enrollment_code;
                    $row['name'] = $laGameEnrollment->user ? $laGameEnrollment->user->name : '';
                    $row['unlock_enrollment_at'] = $laGameEnrollment->unlock_enrollment_at;
                    fputcsv($file, array($row['sr_no'], $row['type'], $row['enrollment_code'], $row['name'], $row['unlock_enrollment_at']));
                    $i++;
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }

        $laGameEnrollments = $laGameEnrollments->paginate(50);
        return view('pages.admin.enrollment-codes.index', compact('laGameEnrollments', 'type'));
    }

    public function create()
    {
        return view('pages.admin.enrollment-codes.create');
    }

    public function store(Request $request)
    {
        $enrollmentCodeId = 0;
        for ($i = 1; $i <= $request->enrollment_code_count; $i++) {
            $checkPreviousenrollment = LaGameEnrollment::orderBy('id', 'desc')->first();
            if ($checkPreviousenrollment) {
                $enrollmentCodeId = $checkPreviousenrollment->id;
            }
            $formattedId = str_pad($enrollmentCodeId, 4, '0', STR_PAD_LEFT);
            LaGameEnrollment::create([
                "type" => $request->type,
                "enrollment_code" => $formattedId,
            ]);
        }

        return redirect()->route('admin.game.enrollments.index')->with('success', 'Code Added');
    }
}
