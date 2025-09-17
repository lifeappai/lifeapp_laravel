<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\School;
use App\Models\LaMission;
use App\Models\Vision;
use App\Models\LaLevel;
use App\Models\SchoolScoreSnapshot;
use App\Models\CoinTransaction;
use App\Models\LaQuestion;
use App\Models\TeacherScoreSnapshot;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CalculateSchoolSScores extends Command
{
    protected $signature = 'calculate:school-sscores';
    protected $description = 'Calculate and store S-Score for all schools for the current month';

    public function handle()
    {
        $now = Carbon::now();
        $month = $now->format('Y-m');

        $levels = LaLevel::all()->keyBy('id');
        $missions = LaMission::select('la_level_id')->whereIn('allow_for', [1, 3])->get();
        $visions = Vision::select('la_level_id')->whereIn('allow_for', [1, 3])->get();
        $quizzes = LaQuestion::select('la_level_id')->where('type', 'quiz')->get();

        // Pre-count challenges per level
        $challengeCounts = [];
        foreach ($levels as $levelId => $level) {
            $challengeCounts[$levelId] = [
                'missions' => 0,
                'visions' => 0,
                'quizzes' => 0
            ];
        }

        foreach ($missions as $mission) {
            if (isset($challengeCounts[$mission->la_level_id])) {
                $challengeCounts[$mission->la_level_id]['missions']++;
            }
        }

        foreach ($visions as $vision) {
            if (isset($challengeCounts[$vision->la_level_id])) {
                $challengeCounts[$vision->la_level_id]['visions']++;
            }
        }

        foreach ($quizzes as $quiz) {
            if (isset($challengeCounts[$quiz->la_level_id])) {
                $challengeCounts[$quiz->la_level_id]['quizzes']++;
            }
        }

        // All schools
        $schools = School::all();

        foreach ($schools as $school) {
            $this->info("Processing school: {$school->id} - {$school->name}");

            // TEACHER COINS
            $teacherSnapshots = TeacherScoreSnapshot::where('month', $month)
                ->whereHas('teacher', function ($q) use ($school) {
                    $q->where('school_id', $school->id);
                })
                ->get();

            $teacherCoins = $teacherSnapshots->sum('assign_task_coins') + $teacherSnapshots->sum('correct_submission_coins');
            $maxTeacherCoins = $teacherSnapshots->sum('max_possible_coins');

            // STUDENT COINS
            $studentCoins = CoinTransaction::whereIn('type', [
                    CoinTransaction::TYPE_MISSION,
                    CoinTransaction::TYPE_QUIZ,
                    CoinTransaction::TYPE_VISION
                ])
                ->whereMonth('created_at', $now->month)
                ->whereYear('created_at', $now->year)
                ->whereHas('user', function ($q) use ($school) {
                    $q->where('school_id', $school->id)
                      ->where('type', 3); // student
                })
                ->sum('amount');

            // MAX STUDENT COINS
            $studentCount = User::where('school_id', $school->id)->where('type', 3)->count();
            $maxStudentCoins = 0;

            foreach ($challengeCounts as $levelId => $counts) {
                if (!isset($levels[$levelId])) continue;

                $level = $levels[$levelId];

                $maxStudentCoins += $studentCount * (
                    $counts['missions'] * $level->mission_points +
                    $counts['visions'] * $level->vision_text_image_points +
                    $counts['quizzes'] * $level->quiz_points
                );
            }

            $totalEarned = $teacherCoins + $studentCoins;
            $totalPossible = $maxTeacherCoins + $maxStudentCoins;

            $sScore = $totalPossible > 0 ? round(($totalEarned / $totalPossible) * 100, 2) : 0;

            // Upsert to DB
            SchoolScoreSnapshot::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'month' => $month,
                ],
                [
                    's_score' => $sScore,
                    'teacher_coins' => $teacherCoins,
                    'student_coins' => $studentCoins,
                    'max_teacher_coins' => $maxTeacherCoins,
                    'max_student_coins' => $maxStudentCoins,
                    'total_coins' => $totalEarned,
                    'updated_at' => now(),
                ]
            );
        }

        $this->info("S-Score calculation completed for month $month.");
    }
}
