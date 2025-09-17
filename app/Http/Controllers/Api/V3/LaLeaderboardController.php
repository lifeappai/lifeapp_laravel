<?php

namespace App\Http\Controllers\Api\V3;

use Illuminate\Http\Request;
use App\Models\TeacherScoreSnapshot;
use Carbon\Carbon;
use App\Models\School;
use App\Models\SchoolScoreSnapshot;
use Illuminate\Support\Facades\DB;

class LaLeaderboardController extends ResponseController
{
    public function getTeacherLeaderboard(Request $request)
    {
        $filter = $request->input('filter', 'monthly'); // default to 'monthly'
        $now = Carbon::now();

        // Determine date range based on filter
        switch ($filter) {
            case 'quarterly':
                $start = $now->copy()->firstOfQuarter();
                $end = $now->copy()->lastOfQuarter();
                break;
            case 'halfyearly':
                $start = $now->month <= 6 ? $now->copy()->startOfYear() : $now->copy()->month(7)->startOfMonth();
                $end = $now->month <= 6 ? $now->copy()->month(6)->endOfMonth() : $now->copy()->endOfYear();
                break;
            case 'yearly':
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                break;
            case 'monthly':
            default:
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
        }

        // Convert to YYYY-MM format array
        $months = [];
        $current = $start->copy();
        while ($current->lte($end)) {
            $months[] = $current->format('Y-m');
            $current->addMonth();
        }

        // Fetch data for teachers in range
        $snapshots = TeacherScoreSnapshot::with('teacher')
            ->whereIn('month', $months)
            ->get()
            ->groupBy('user_id');

        // Aggregate and average t-scores
        $rankedData = $snapshots->map(function ($entries, $userId) {
            $teacher = $entries->first()->teacher;

            $avgTScore = round($entries->avg('t_score'), 4);
            $totalAssign = $entries->sum('assign_task_coins');
            $totalCorrect = $entries->sum('correct_submission_coins');
            $totalMax = $entries->sum('max_possible_coins');
            $totalEarned = $totalAssign + $totalCorrect;

            return [
                'teacher_id' => $userId,
                'name' => $teacher->name ?? 'N/A',
                'school_id' => $teacher->school_id ?? null,
                't_score' => $avgTScore,
                'assign_task_coins' => $totalAssign,
                'correct_submission_coins' => $totalCorrect,
                'max_possible_coins' => $totalMax,
                'total_earned_coins' => $totalEarned,
                'image_path' => $teacher->image_path ?? null,
            ];
        })->sortByDesc('t_score')->values()->all();

        // Add ranks
        foreach ($rankedData as $i => &$item) {
            $item['rank'] = $i + 1;
        }

        return response()->json($rankedData);
    }

    public function schoolLeaderboard(Request $request)
    {
        $filter = $request->input('filter', 'monthly'); // default to monthly
        $now = Carbon::now();

        // Determine start & end dates based on filter
        switch ($filter) {
            case 'quarterly':
                $start = $now->copy()->firstOfQuarter();
                $end = $now->copy()->lastOfQuarter();
                break;
            case 'halfyearly':
                $start = $now->month <= 6 ? $now->copy()->startOfYear() : $now->copy()->month(7)->startOfMonth();
                $end = $now->month <= 6 ? $now->copy()->month(6)->endOfMonth() : $now->copy()->endOfYear();
                break;
            case 'yearly':
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                break;
            case 'monthly':
            default:
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
        }

        // Convert to 'YYYY-MM' format for filtering
        $months = [];
        $current = $start->copy();
        while ($current->lte($end)) {
            $months[] = $current->format('Y-m');
            $current->addMonth();
        }

        // Fetch data from snapshots within selected months
        $snapshots = SchoolScoreSnapshot::whereIn('month', $months)->get()->groupBy('school_id');

        // Get school names
        $schoolMap = School::pluck('name', 'id');

        // Process and average/aggregate data
        $ranked = $snapshots->map(function ($entries, $schoolId) use ($schoolMap) {
            $totalSScore = $entries->sum('s_score');
            $count = $entries->count();
            $totalEarned = $entries->sum('total_coins');
            $studentCoins = $entries->sum('student_coins');
            $teacherCoins = $entries->sum('teacher_coins');
            $maxStudent = $entries->sum('max_student_coins');
            $maxTeacher = $entries->sum('max_teacher_coins');

            return [
                'school_id' => $schoolId,
                'school_name' => $schoolMap[$schoolId] ?? 'Unknown',
                'total_coins' => $totalEarned,
                'student_coins' => $studentCoins,
                'teacher_coins' => $teacherCoins,
                'max_student_coins' => $maxStudent,
                'max_teacher_coins' => $maxTeacher,
                's_score' => $count > 0 ? round($totalSScore / $count, 2) : 0,
            ];
        })->sortByDesc('s_score')->values();

        // Add rank
        $data = $ranked->map(function ($item, $index) {
            return array_merge($item, ['rank' => $index + 1]);
        });

        return response()->json([
            'success' => true,
            'filter' => $filter,
            'months' => $months,
            'data' => $data
        ]);
    }

}
