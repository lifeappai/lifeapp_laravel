<?php

namespace App\Http\Controllers\Api\V3;

use App\Enums\GameType;
use App\Enums\StatusEnum;
use App\Models\LaMission;
use App\Models\LaQuestion;
use App\Models\LaSubject;
use App\Models\Vision;
use App\Models\VisionUserStatus;
use Illuminate\Support\Facades\Auth;

class GameReportController extends ResponseController
{

    public function index()
    {
        $laSubjects = LaSubject::orderBy('index')->where('status', StatusEnum::ACTIVE)->get();
        $response = [];
        foreach ($laSubjects as $laSubject) {
            // vision
            $response[$laSubject->default_title]['vision']['pending'] = $this->pendingVisionCount($laSubject);
            $response[$laSubject->default_title]['vision']['complete'] = $this->completeVisionCount($laSubject);
            
            //missions
            $response[$laSubject->default_title]['mission']['pending'] = $this->pendingMissionCount($laSubject, GameType::MISSION);
            $response[$laSubject->default_title]['mission']['complete'] = $this->completeMissionCount($laSubject, GameType::MISSION);

            //quiz
            $response[$laSubject->default_title]['quiz']['pending'] =  $this->pendingQuizCount($laSubject, GameType::QUIZ);
            $response[$laSubject->default_title]['quiz']['complete'] =  $this->completeQuizCount($laSubject, GameType::QUIZ);

            //riddles
            $response[$laSubject->default_title]['riddle']['pending'] = $this->pendingQuizCount($laSubject, GameType::RIDDLE);
            $response[$laSubject->default_title]['riddle']['complete'] = $this->completeQuizCount($laSubject, GameType::RIDDLE);

            //puzzles
            $response[$laSubject->default_title]['puzzle']['pending'] = $this->pendingQuizCount($laSubject, GameType::PUZZLE);
            $response[$laSubject->default_title]['puzzle']['complete'] = $this->completeQuizCount($laSubject, GameType::PUZZLE);

            //jigyasa
            $response[$laSubject->default_title]['jigyasa']['pending'] = $this->pendingMissionCount($laSubject, GameType::JIGYASA);
            $response[$laSubject->default_title]['jigyasa']['complete'] = $this->completeMissionCount($laSubject, GameType::JIGYASA);

            //pragya
            $response[$laSubject->default_title]['pragya']['pending'] = $this->pendingMissionCount($laSubject, GameType::PRAGYA);
            $response[$laSubject->default_title]['pragya']['complete'] = $this->completeMissionCount($laSubject, GameType::PRAGYA);
        }
        return $this->sendResponse($response, "Game Reports");
    }

    public function completeQuizCount(LaSubject $laSubject, $type)
    {
        $quizCompleteCount = LaQuestion::where('type', $type)->where('status', StatusEnum::ACTIVE)->where('la_subject_id', $laSubject->id)
            ->whereHas('laTopic', function ($query) {
                $query->where('allow_for', GameType::ALLOW_FOR['ALL'])
                    ->orWhereHas('laTopicAssigns', function ($subQuery) {
                        $subQuery->where('user_id', Auth::user()->id);
                    });
            })
            ->whereHas('quizGameQuestionAnswer', function ($query) {
                $query->where('user_id', Auth::id())->whereHas('laQuizGame', function ($subQuery) {
                    $subQuery->where('user_id', Auth::id());
                });
            })->count();
        return $quizCompleteCount;
    }

    public function pendingQuizCount(LaSubject $laSubject, $type)
    {
        $quizPendingCount = LaQuestion::where('type', $type)->where('status', StatusEnum::ACTIVE)->where('la_subject_id', $laSubject->id)
            ->whereHas('laTopic', function ($query) {
                $query->where('allow_for', GameType::ALLOW_FOR['ALL'])
                    ->orWhereHas('laTopicAssigns', function ($subQuery) {
                        $subQuery->where('user_id', Auth::user()->id);
                    });
            })
            ->whereDoesntHave('quizGameQuestionAnswer', function ($query) {
                $query->where('user_id', Auth::id())->whereHas('laQuizGame', function ($subQuery) {
                    $subQuery->where('user_id', Auth::id());
                });
            })->count();
        return $quizPendingCount;
    }
    public function pendingMissionCount(LaSubject $laSubject, $type)
    {
        $missionPendingCount = LaMission::where('la_subject_id', $laSubject->id)
            ->where(function ($query) {
                $query->where('allow_for', GameType::ALLOW_FOR['ALL'])
                    ->orWhereHas('laMissionAssigns', function ($subQuery) {
                        $subQuery->where('user_id', Auth::user()->id);
                    });
            })
            ->whereDoesntHave('missionCompletes', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('status', StatusEnum::ACTIVE)
            ->where('type', $type)
            ->count();
        return $missionPendingCount;
    }

    public function completeMissionCount(LaSubject $laSubject, $type)
    {
        $missionCompleteCount =  LaMission::where('la_subject_id', $laSubject->id)
            ->where(function ($query) {
                $query->where('allow_for', GameType::ALLOW_FOR['ALL'])
                    ->orWhereHas('laMissionAssigns', function ($subQuery) {
                        $subQuery->where('user_id', Auth::user()->id);
                    });
            })
            ->whereHas('missionCompletes', function ($query) {
                $query->where('user_id', Auth::id());
            })->where('status', StatusEnum::ACTIVE)->where('type', $type)->count();
        return $missionCompleteCount;
    }

    public function pendingVisionCount(LaSubject $laSubject)
    {
        return Vision::where('la_subject_id', $laSubject->id)
            ->where('status', StatusEnum::ACTIVE)
            ->where(function ($query) {
                $query->where('allow_for', GameType::ALLOW_FOR['ALL'])
                    ->orWhereHas('assignments', function ($subQuery) {
                        $subQuery->where('student_id', Auth::id());
                    });
            })
            ->whereDoesntHave('userStatuses', function ($query) {
                $query->where('user_id', Auth::id())
                    ->whereIn('status', ['completed']);
            })
            ->count();
    }

    public function completeVisionCount(LaSubject $laSubject)
    {
        return VisionUserStatus::whereHas('vision', function ($q) use ($laSubject) {
                $q->where('la_subject_id', $laSubject->id)
                ->where('status', StatusEnum::ACTIVE)
                ->where(function ($query) {
                    $query->where('allow_for', GameType::ALLOW_FOR['ALL'])
                        ->orWhereHas('assignments', function ($subQuery) {
                            $subQuery->where('student_id', Auth::id());
                        });
                });
            })
            ->where('user_id', Auth::id())
            ->where('status', 'completed')
            ->count();
    }
}
