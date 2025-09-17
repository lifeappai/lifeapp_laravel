<?php

namespace App\Http\Controllers\API\V3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chapter;
use App\Enums\UserType;

class ChapterController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$user || $user->type !== UserType::Teacher) {
            return response()->json([
                'error' => 'Only teachers can access this endpoint.'
            ], 403);
        }

        $request->validate([
            'la_grade_id'   => 'required|exists:la_grades,id',
            'la_subject_id' => 'nullable|exists:la_subjects,id',
        ]);

        $query = Chapter::where('la_board_id', $user->la_board_id)
                        ->where('la_grade_id', $request->la_grade_id);

        if ($request->filled('la_subject_id')) {
            $query->where('la_subject_id', $request->la_subject_id);
        }

        $chapters = $query->select(
            'id',
            'title',
            'la_board_id',
            'la_grade_id',
            'la_subject_id'
        )->get();

        return response()->json([
            'chapters' => $chapters,
        ]);
    }
}
