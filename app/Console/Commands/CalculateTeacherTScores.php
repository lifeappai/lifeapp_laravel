<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\LaMission;
use App\Models\Vision;
use App\Models\LaTeacherGrade;
use App\Models\LaLevel;
use App\Models\CoinTransaction;
use App\Models\TeacherScoreSnapshot;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CalculateTeacherTScores extends Command
{
    protected $signature = 'calculate:teacher-tscores';
    protected $description = 'Calculate and store T-Score for teachers for the current month';

    public function handle()
    {
        $now = Carbon::now();
        $month = $now->format('Y-m');

        $teachers = User::where('type', 5)->get();

        foreach ($teachers as $teacher) {
            $this->info("Processing teacher: {$teacher->id} - {$teacher->name}");

            // Get subjects/grades/sections the teacher is mapped to
            $mappings = LaTeacherGrade::where('user_id', $teacher->id)->get();

            $studentCount = 0;

            foreach ($mappings as $map) {
                $studentsInClass = User::where('type', 3) //type 3 = student
                    ->where('la_grade_id', $map->la_grade_id)
                    ->where('la_section_id', $map->la_section_id)
                    ->where('school_id', $teacher->school_id)
                    ->count();

                $studentCount += $studentsInClass;
            }

            $maxPossibleCoins = 0;
            $levels = LaLevel::all();

            foreach ($levels as $level) {
                $lid = $level->id;

                $missionCount = LaMission::where('la_level_id', $lid)
                    ->whereIn('allow_for', [2]) // All or teacher
                    ->count();

                $visionCount = Vision::where('la_level_id', $lid)
                    ->whereIn('allow_for', [1, 2]) // All or teacher
                    ->count();

                $totalChallenges = $missionCount + $visionCount;

                $coinsForLevel = $studentCount * $totalChallenges * (
                    $level->teacher_assign_points + $level->teacher_correct_submission_points
                );

                $maxPossibleCoins += $coinsForLevel;
            }

            $assignTaskCoins = CoinTransaction::where('user_id', $teacher->id)
                ->where('type', CoinTransaction::TYPE_ASSIGN_TASK)
                ->whereMonth('created_at', $now->month)
                ->whereYear('created_at', $now->year)
                ->sum('amount');

            $correctSubmissionCoins = CoinTransaction::where('user_id', $teacher->id)
                ->where('type', CoinTransaction::TYPE_CORRECT_SUBMISSION)
                ->whereMonth('created_at', $now->month)
                ->whereYear('created_at', $now->year)
                ->sum('amount');

            $earnedCoins = $assignTaskCoins + $correctSubmissionCoins;
            $tScore = $maxPossibleCoins > 0 ? round($earnedCoins / $maxPossibleCoins, 4) : 0;

            // Upsert into teacher_score_snapshots
            TeacherScoreSnapshot::updateOrCreate(
                [
                    'user_id' => $teacher->id,
                    'month' => $month,
                ],
                [
                    't_score' => $tScore,
                    'assign_task_coins' => $assignTaskCoins,
                    'correct_submission_coins' => $correctSubmissionCoins,
                    'max_possible_coins' => $maxPossibleCoins,
                    'updated_at' => now(),
                ]
            );
        }

        $this->info("T-Score calculation completed for month $month.");

        // Step 2: Assign badges based on ranking
        $this->assignBadges($month);
    }

    protected function assignBadges($month)
    {
        $snapshots = TeacherScoreSnapshot::where('month', $month)
            ->orderByDesc('t_score')
            ->get();

        $count = $snapshots->count();
        if ($count === 0) return;

        $chunkSize = ceil($count / 4);

        foreach ($snapshots as $index => $snapshot) {
            if ($index < $chunkSize) {
                $badge = 'Master Teacher';
            } elseif ($index < $chunkSize * 2) {
                $badge = 'Proactive Teacher';
            } elseif ($index < $chunkSize * 3) {
                $badge = 'Consistent Teacher';
            } else {
                $badge = 'Emerging Teacher';
            }

            $snapshot->update(['engagement_badge' => $badge]);
        }

        $this->info("Badges assigned based on T-Score ranking.");
    }
}
