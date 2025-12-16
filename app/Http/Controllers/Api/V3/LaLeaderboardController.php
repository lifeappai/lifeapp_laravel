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
        $filter = $request->input('filter', 'monthly');
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 20); // default 20

        $now = Carbon::now();

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
            default: // monthly
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
        }

        $months = [];
        $current = $start->copy();
        while ($current->lte($end)) {
            $months[] = $current->format('Y-m');
            $current->addMonth();
        }

        $snapshots = TeacherScoreSnapshot::with('teacher')
            ->whereIn('month', $months)
            ->get()
            ->groupBy('user_id');

        $rankedData = $snapshots->map(function ($entries, $userId) {
            $teacher = $entries->first()->teacher;

            return [
                'teacher_id' => $userId,
                'name' => $teacher->name ?? 'N/A',
                'school_id' => $teacher->school_id ?? null,
                't_score' => round($entries->avg('t_score') * 100, 2),
                'assign_task_coins' => $entries->sum('assign_task_coins'),
                'correct_submission_coins' => $entries->sum('correct_submission_coins'),
                'max_possible_coins' => $entries->sum('max_possible_coins'),
                'total_earned_coins' => $entries->sum('assign_task_coins') + $entries->sum('correct_submission_coins'),
                'image_path' => $teacher->image_path ?? null,
            ];
        })->sortByDesc('t_score')->values()->all();

        // Add rank
        foreach ($rankedData as $i => &$item) {
            $item['rank'] = $i + 1;
        }

        // Apply pagination manually
        $total = count($rankedData);
        $offset = ($page - 1) * $perPage;
        $paginatedItems = array_slice($rankedData, $offset, $perPage);

        return response()->json([
            "success" => true,
            "page" => $page,
            "per_page" => $perPage,
            "total" => $total,
            "total_pages" => ceil($total / $perPage),
            "filter" => $filter,
            "months" => $months,
            "data" => $paginatedItems,
        ]);
    }

    public function schoolLeaderboard(Request $request)
    {
        $filter = $request->input('filter', 'monthly'); 
        $now = Carbon::now();

        // Determine start & end dates
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

        // Convert to YYYY-MM
        $months = [];
        $current = $start->copy();
        while ($current->lte($end)) {
            $months[] = $current->format('Y-m');
            $current->addMonth();
        }

        // Fetch snapshots and group
        $snapshots = SchoolScoreSnapshot::whereIn('month', $months)->get()->groupBy('school_id');
        $schoolMap = School::pluck('name', 'id');

        // Rank
        $ranked = $snapshots->map(function ($entries, $schoolId) use ($schoolMap) {
            $totalSScore = $entries->sum('s_score');
            $count = $entries->count();
            return [
                'school_id' => $schoolId,
                'school_name' => $schoolMap[$schoolId] ?? 'Unknown',
                'total_coins' => $entries->sum('total_coins'),
                'student_coins' => $entries->sum('student_coins'),
                'teacher_coins' => $entries->sum('teacher_coins'),
                'max_student_coins' => $entries->sum('max_student_coins'),
                'max_teacher_coins' => $entries->sum('max_teacher_coins'),
                's_score' => $count > 0 ? round(($totalSScore / $count) * 100, 2) : 0,
            ];
        })->sortByDesc('s_score')->values();

        // Add rank
        $ranked = $ranked->map(function ($item, $index) {
            $item['rank'] = $index + 1;
            return $item;
        });

        // 🔹 Pagination Logic
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $perPage;

        $paginated = $ranked->slice($offset, $perPage)->values();

        return response()->json([
            'success' => true,
            'filter' => $filter,
            'months' => $months,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $ranked->count(),
            'last_page' => ceil($ranked->count() / $perPage),
            'data' => $paginated
        ]);
    }

}
