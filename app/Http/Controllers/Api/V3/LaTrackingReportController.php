<?php

namespace App\Http\Controllers\Api\V3;

use App\Enums\GameType;
use App\Enums\StatusEnum;
use App\Enums\UserType;
use App\Http\Resources\PublicUserResrouce;
use App\Models\LaMission;
use App\Models\LaTeacherGrade;
use App\Models\LaTopic;
use App\Models\User;
use App\Models\Vision;
use App\Models\VisionAssign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CoinTransaction;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class LaTrackingReportController extends ResponseController
{
    /* =========================
       📊 1. All Students Report
    ========================== */
    public function allStudents(Request $request)
    {
        // [$startDate, $endDate] = $this->getTimelineDates(timeline: $request->timeline ?? 'yearly');
        if ($request->filled('startDate') && $request->filled('endDate')) {
            $startDate = $request->startDate;
            $endDate = $request->endDate;
        } else {
            [$startDate, $endDate] = $this->getTimelineDates(
                timeline: $request->timeline ?? 'yearly'
            );
        }

        $teacherId = Auth::user()->id;
        $subjectId = $request->subject_id;

        $laTeacherSections = LaTeacherGrade::where('user_id', $teacherId)->pluck('la_section_id')->toArray();
        $laTeacherGrades = LaTeacherGrade::where('user_id', $teacherId)->pluck('la_grade_id')->toArray();
        Log::info('Teacher Sections', [
            'grades' => $laTeacherGrades,
            'sections' => $laTeacherSections,
            'subjectId' => $subjectId
        ]);

        $users = User::where('school_id', Auth::user()->school_id)
            ->whereIn('la_grade_id', $laTeacherGrades)
            ->whereIn('la_section_id', $laTeacherSections)
            ->where('type', UserType::Student)
            ->get();

        $response = [];

        foreach ($users as $user) {
            $data['user'] = new PublicUserResrouce($user);
            // ===== MISSIONS =====
            $data['mission_assigned'] = \DB::table('la_mission_assigns')
                ->join('la_missions', 'la_mission_assigns.la_mission_id', '=', 'la_missions.id')
                ->where('la_missions.type', 1)
                ->when($subjectId, function ($query) use ($subjectId) {
                    $query->where('la_missions.la_subject_id', $subjectId);
                })
                ->where('la_mission_assigns.user_id', $user->id)
                ->where('la_mission_assigns.teacher_id', $teacherId)
                ->whereBetween('la_mission_assigns.created_at', [$startDate, $endDate])
                ->count();

            $data['mission'] = $this->completeMissionCount($user, $startDate, $endDate, $subjectId);
            $data['mission_incomplete'] = max($data['mission_assigned'] - $data['mission'], 0);

            $data['mission_completion_rate'] = $data['mission_assigned'] > 0
                ? round(($data['mission'] / $data['mission_assigned']) * 100, 2)
                : 0;

            // ===== VISIONS =====
            $data['vision_assigned'] = \DB::table('vision_assigns')
                ->join('visions', 'vision_assigns.vision_id', '=', 'visions.id')
                ->when($request->has('subject_id'), function ($query) use ($subjectId) {
                    $query->where('visions.la_subject_id', $subjectId);
                })
                ->where('vision_assigns.teacher_id', $teacherId)
                ->where('vision_assigns.student_id', $user->id)
                ->whereBetween('vision_assigns.created_at', [$startDate, $endDate])
                ->count();

            $data['vision'] = $this->completeVisionCount($user, $startDate, $endDate, $subjectId);
            $data['vision_incomplete'] = max($data['vision_assigned'] - $data['vision'], 0);

            $data['vision_completion_rate'] = $data['vision_assigned'] > 0
                ? round(($data['vision'] / $data['vision_assigned']) * 100, 2)
                : 0;

            // ===== QUIZZES =====
            $data['quiz'] = $user->laQuizGameResults()
                ->when($subjectId, function ($query) use ($subjectId) {
                    $query->whereHas('laQuizGame', function ($q) use ($subjectId) {
                        $q->where('la_subject_id', $subjectId);
                    });
                })
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            $data['riddle'] = $this->completeQuizCount($user, GameType::RIDDLE, $startDate, $endDate, $subjectId);
            $data['puzzle'] = $this->completeQuizCount($user, GameType::PUZZLE, $startDate, $endDate, $subjectId);

            // ===== COINS =====
            $data['coins'] = CoinTransaction::where('user_id', $user->id)
                ->where('amount', '>', 0)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount');

            $data['coins_mission'] = $this->getCoinsByType($user->id, 'App\\Models\\LaMissionComplete', $startDate, $endDate);
            $data['coins_vision'] = $this->getCoinsByType($user->id, 'App\\Models\\VisionQuestionAnswer', $startDate, $endDate);
            $data['coins_quiz'] = $this->getCoinsByType($user->id, 'App\\Models\\LaQuizGameResult', $startDate, $endDate);

            $data['coins_redeemed'] = CoinTransaction::where('user_id', $user->id)
                ->where('amount', '<', 0)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount');

            $response['student'][] = $data;
        }

        return $this->sendResponse($response, "All students report");
    }

    /* =========================
       📊 2. Class Students Report
    ========================== */
    public function classStudents(LaTeacherGrade $laTeacherGrade, Request $request)
    {
        [$startDate, $endDate] = $this->getTimelineDates($request->timeline ?? 'yearly');
        $teacherId = Auth::user()->id;
        $subjectId = $request->subject_id;

        $schoolId = $laTeacherGrade->user->school->id ?? null;

        $users = User::where('school_id', $schoolId)
            ->where('la_grade_id', $laTeacherGrade->la_grade_id)
            ->where('la_section_id', $laTeacherGrade->la_section_id)
            ->where('type', UserType::Student)
            ->get();

        $response = [];
        $totalMissionAssigned = $totalMissionCompleted = 0;
        $totalVisionAssigned = $totalVisionCompleted = 0;
        $totalCoins = $totalCoinsMission = $totalCoinsVision = $totalCoinsQuiz = $totalCoinsRedeemed = 0;
        $totalQuiz = $totalPuzzle = 0;

        foreach ($users as $user) {
            $data['user'] = new PublicUserResrouce($user);

            // ===== MISSIONS =====
            $data['mission_assigned'] = \DB::table('la_mission_assigns')
                ->join('la_missions', 'la_mission_assigns.la_mission_id', '=', 'la_missions.id')
                ->where('la_missions.type', 1) // ✅ Only missions (exclude Jigyasa, Pragya)
                ->where('la_mission_assigns.user_id', $user->id)
                ->where('la_mission_assigns.teacher_id', $teacherId)
                ->whereBetween('la_mission_assigns.created_at', [$startDate, $endDate])
                ->count();

            $data['mission'] = $this->completeMissionCount($user, $startDate, $endDate, $subjectId);
            $data['mission_incomplete'] = max($data['mission_assigned'] - $data['mission'], 0);
            $data['mission_completion_rate'] = $data['mission_assigned'] > 0
                ? round(($data['mission'] / $data['mission_assigned']) * 100, 2)
                : 0;

            $totalMissionAssigned += $data['mission_assigned'];
            $totalMissionCompleted += $data['mission'];

            // ===== VISIONS =====
            $data['vision_assigned'] = \DB::table('vision_assigns')
                ->where('teacher_id', $teacherId)
                ->where('student_id', $user->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            $data['vision'] = $this->completeVisionCount($user, $startDate, $endDate, $subjectId);
            $data['vision_incomplete'] = max($data['vision_assigned'] - $data['vision'], 0);
            $data['vision_completion_rate'] = $data['vision_assigned'] > 0
                ? round(($data['vision'] / $data['vision_assigned']) * 100, 2)
                : 0;

            $totalVisionAssigned += $data['vision_assigned'];
            $totalVisionCompleted += $data['vision'];

            // ===== QUIZZES =====
            $data['quiz'] = $user->laQuizGameResults()
                ->when($subjectId, function ($query) use ($subjectId) {
                    $query->whereHas('laQuizGame', function ($q) use ($subjectId) {
                        $q->where('la_subject_id', $subjectId);
                    });
                })
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
            $totalQuiz += $data['quiz'];

            $data['riddle'] = $this->completeQuizCount($user, GameType::RIDDLE, $startDate, $endDate, $subjectId);
            $data['puzzle'] = $this->completeQuizCount($user, GameType::PUZZLE, $startDate, $endDate, $subjectId);
            $totalPuzzle += $data['puzzle'];

            // ===== COINS =====
            $data['coins'] = CoinTransaction::where('user_id', $user->id)
                ->where('amount', '>', 0)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount');
            $totalCoins += $data['coins'];

            $data['coins_mission'] = $this->getCoinsByType($user->id, 'App\\Models\\LaMissionComplete', $startDate, $endDate);
            $data['coins_vision'] = $this->getCoinsByType($user->id, 'App\\Models\\VisionQuestionAnswer', $startDate, $endDate);
            $data['coins_quiz'] = $this->getCoinsByType($user->id, 'App\\Models\\LaQuizGameResult', $startDate, $endDate);
            $data['coins_redeemed'] = CoinTransaction::where('user_id', $user->id)
                ->where('amount', '<', 0)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount');

            $totalCoinsMission += $data['coins_mission'];
            $totalCoinsVision += $data['coins_vision'];
            $totalCoinsQuiz += $data['coins_quiz'];
            $totalCoinsRedeemed += $data['coins_redeemed'];

            $response['student'][] = $data;
        }

        if (!empty($response['student'])) {
            $response['total_mission_assigned'] = $totalMissionAssigned;
            $response['total_mission'] = $totalMissionCompleted;
            $response['total_mission_incomplete'] = max($totalMissionAssigned - $totalMissionCompleted, 0);

            $response['total_vision_assigned'] = $totalVisionAssigned;
            $response['total_vision'] = $totalVisionCompleted;
            $response['total_vision_incomplete'] = max($totalVisionAssigned - $totalVisionCompleted, 0);

            $response['mission_completion_rate'] = $totalMissionAssigned > 0
                ? round(($totalMissionCompleted / $totalMissionAssigned) * 100, 2)
                : 0;

            $response['vision_completion_rate'] = $totalVisionAssigned > 0
                ? round(($totalVisionCompleted / $totalVisionAssigned) * 100, 2)
                : 0;

            $response['total_quiz'] = $totalQuiz;
            $response['total_puzzle'] = $totalPuzzle;
            $response['total_coins'] = $totalCoins;
            $response['total_coins_mission'] = $totalCoinsMission;
            $response['total_coins_vision'] = $totalCoinsVision;
            $response['total_coins_quiz'] = $totalCoinsQuiz;
            $response['total_coins_redeemed'] = $totalCoinsRedeemed;
        }

        return $this->sendResponse($response, "Class students report");
    }

    /* =========================
       🔹 Helper Methods
    ========================== */
    private function getCoinsByType($userId, $type, $startDate, $endDate)
    {
        return CoinTransaction::where('user_id', $userId)
            ->where('coinable_type', $type)
            ->where('amount', '>', 0)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');
    }

    public function completeMissionCount(User $user, $startDate, $endDate, $subjectId = null)
    {
        return LaMission::whereIn('allow_for', [GameType::ALLOW_FOR['BY_TEACHER'], GameType::ALLOW_FOR['ALL']])
            //if subject id is provided then filtering missions by subject id
            ->when($subjectId, function ($query) use ($subjectId) {
                $query->where('la_subject_id', $subjectId);
            })
            ->whereHas('missionCompletes', function ($q) use ($user, $startDate, $endDate) {
                $q->where('user_id', $user->id)
                    ->whereNotNull('approved_at')
                    ->whereBetween('approved_at', [$startDate, $endDate]);
            })
            ->whereHas('laMissionAssigns', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->where('teacher_id', Auth::user()->id);
            })
            ->where('status', StatusEnum::ACTIVE)
            ->where('type', GameType::MISSION)
            ->count();
    }

    public function completeVisionCount(User $user, $startDate, $endDate, $subjectId = null)
    {
        return Vision::whereIn('allow_for', [GameType::ALLOW_FOR['BY_TEACHER'], GameType::ALLOW_FOR['ALL']])
            //if subject id is provided then filtering vision by subject id
            ->when($subjectId, function ($query) use ($subjectId) {
                $query->where('la_subject_id', $subjectId);
            })
            ->whereHas('userStatuses', function ($q) use ($user, $startDate, $endDate) {
                $q->where('user_id', $user->id)
                    ->where('status', 'completed')
                    ->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->whereHas('visionAssigns', function ($q) use ($user) {
                $q->where('student_id', $user->id)
                    ->where('teacher_id', Auth::user()->id);
            })
            ->where('status', StatusEnum::ACTIVE)
            ->count();
    }

    public function completeQuizCount(User $user, $type, $startDate, $endDate, $subjectId = null)
    {
        return LaTopic::whereIn('allow_for', [GameType::ALLOW_FOR['BY_TEACHER'], GameType::ALLOW_FOR['ALL']])
            ->when($subjectId, function ($query) use ($subjectId) {
                $query->where('la_subject_id', $subjectId);
            })
            ->whereHas('laQuizGames', function ($q) use ($user, $type, $startDate, $endDate) {
                $q->where('user_id', $user->id)
                    ->where('type', $type)
                    ->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->whereHas('laTopicAssigns', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->where('teacher_id', Auth::user()->id);
            })
            ->where('status', StatusEnum::ACTIVE)
            ->count();
    }

    private function getTimelineDates($timeline)
    {
        $now = Carbon::now();

        switch ($timeline) {
            case 'monthly':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;

            case 'quarterly':
                $currentQuarter = ceil($now->month / 3);
                $start = Carbon::create($now->year, ($currentQuarter - 1) * 3 + 1, 1)->startOfMonth();
                $end = (clone $start)->addMonths(3)->endOfMonth();
                break;

            case 'halfyearly':
                if ($now->month <= 6) {
                    // First half (Jan–Jun)
                    $start = Carbon::create($now->year, 1, 1)->startOfMonth();
                    $end = Carbon::create($now->year, 6, 30)->endOfMonth();
                } else {
                    // Second half (Jul–Dec)
                    $start = Carbon::create($now->year, 7, 1)->startOfMonth();
                    $end = Carbon::create($now->year, 12, 31)->endOfMonth();
                }
                break;

            default:
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                Log::info("Timeline dates: start={$start}, end={$end}");
                break;
        }

        return [$start, $end];
    }

}
