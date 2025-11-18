<?php

namespace App\Http\Controllers\API\V3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chapter;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserType;

class ChapterController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // ✅ Ensure only teachers can access
        if (!$user || $user->type !== UserType::Teacher) {
            return response()->json([
                'error' => 'Only teachers can access this endpoint.'
            ], 403);
        }

        // ✅ Validation
        $request->validate([
            'la_board_id'   => 'nullable|exists:la_boards,id',
            'la_grade_id'   => 'required|exists:la_grades,id',
            'la_subject_id' => 'nullable|exists:la_subjects,id',
        ]);

        // ✅ Use board_id from request if given, else fallback to user's board_id
        $boardId = $request->input('la_board_id', $user->la_board_id);

        // ✅ Build query
        $query = Chapter::where('la_board_id', $boardId)
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
        )->orderBy('id')->get();

        return response()->json([
            'chapters' => $chapters,
        ]);
    }
}
