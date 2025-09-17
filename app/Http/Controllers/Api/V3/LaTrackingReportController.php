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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaTrackingReportController extends ResponseController
{
    public function allStudents(Request $request)
    {
        $laTeacherSections = LaTeacherGrade::where('user_id', Auth::user()->id)->pluck('la_section_id')->toArray();
        $laTeacherGrades = LaTeacherGrade::where('user_id', Auth::user()->id)->pluck('la_grade_id')->toArray();
        $users = User::where('school_id', Auth::user()->school_id)->whereIn('la_grade_id', $laTeacherGrades)->whereIn('la_section_id', $laTeacherSections)->where('type', UserType::Student)->get();
        $response = [];
        $i = 1;
        foreach ($users as $user) {
            $data['user'] = new PublicUserResrouce($user);
            $data['vision'] = $this->completeVisionCount($user);
            $data['mission'] = $this->completeMissionCount($user);
            $data['quiz'] = $user->laQuizGameResults ? $user->laQuizGameResults->count() : 0;
            $data['riddle'] = $this->completeQuizCount($user, GameType::RIDDLE);
            $data['puzzle'] = $this->completeQuizCount($user, GameType::PUZZLE);
            $data['coins'] = $user->earn_coins;
            $i++;
            $response['student'][]= $data;
        }
        return  $this->sendResponse($response, "all students");
    }

    public function classStudents(LaTeacherGrade $laTeacherGrade)
    {
        $schoolId = $laTeacherGrade->user->school->id ?? null;
        $users = User::where('school_id', $schoolId)->where('la_grade_id', $laTeacherGrade->la_grade_id)->where('la_section_id', $laTeacherGrade->la_section_id)->where('type', UserType::Student)->get();
        $response = null;
        $i = 1;
        $totalVision = 0;
        $totalMission = 0;
        $totalQuiz = 0;
        $totalPuzzle = 0;
        $totalCoins = 0;
        foreach ($users as $user) {
            $data['user'] = new PublicUserResrouce($user);
            $data['vision'] = $this->completeVisionCount($user);
            $totalVision += $data['vision'];
            $data['mission'] = $this->completeMissionCount($user);
            $totalMission += $data['mission'];
            $data['quiz'] = $user->laQuizGameResults ? $user->laQuizGameResults->count() : 0;
            $totalQuiz += $data['quiz'];
            $data['riddle'] = $this->completeQuizCount($user, GameType::RIDDLE);
            $data['puzzle'] = $this->completeQuizCount($user, GameType::PUZZLE);
            $totalPuzzle += $data['puzzle'];
            $data['coins'] = $user->earn_coins;
            $totalCoins += $user->earn_coins;
            $i++;
            $response['student'][] = $data;
        }
        if ($response) {
            $response['total_vision'] = $totalVision;
            $response['total_mission'] = $totalMission;
            $response['total_quiz'] = $totalQuiz;
            $response['total_puzzle'] = $totalPuzzle;
            $response['total_coins'] = $totalCoins;
        }

        return  $this->sendResponse($response, "class students");
    }

    public function completeMissionCount(User $user)
    {
        $missionCompleteCount =  LaMission::where('allow_for', GameType::ALLOW_FOR['BY_TEACHER'])
            ->whereHas('missionCompletes', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereHas('laMissionAssigns', function ($query) use ($user) {
                $query->where('user_id', $user->id)->where('teacher_id', Auth::user()->id);
            })
            ->where('status', StatusEnum::ACTIVE)->where('type', GameType::MISSION)->count();

        return $missionCompleteCount;
    }


    public function completeQuizCount(User $user, $type)
    {
        $quizCompleteCount =  LaTopic::where('allow_for', GameType::ALLOW_FOR['BY_TEACHER'])
            ->whereHas('laQuizGames', function ($query) use ($user, $type) {
                $query->where('user_id', $user->id)->where('type', $type);
            })
            ->whereHas('laTopicAssigns', function ($query) use ($user) {
                $query->where('user_id', $user->id)->where('teacher_id', Auth::user()->id);
            })
            ->where('status', StatusEnum::ACTIVE)->count();
        return $quizCompleteCount;
    }

    public function completeVisionCount(User $user)
    {
        return \DB::table('vision_user_statuses')
            ->join('vision_assigns', function ($join) use ($user) {
                $join->on('vision_assigns.vision_id', '=', 'vision_user_statuses.vision_id')
                    ->where('vision_assigns.student_id', $user->id)
                    ->where('vision_assigns.teacher_id', Auth::id());
            })
            ->join('visions', 'visions.id', '=', 'vision_user_statuses.vision_id')
            ->where('visions.status', StatusEnum::ACTIVE)
            ->where('visions.allow_for', GameType::ALLOW_FOR['BY_TEACHER'])
            ->where('vision_user_statuses.user_id', $user->id)
            ->where('vision_user_statuses.status', 'completed')
            ->count();
    }
}
