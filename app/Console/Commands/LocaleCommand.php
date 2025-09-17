<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Movie;
use App\Models\LevelTranslation;
use App\Models\Mission;
use App\Models\MissionImage;
use App\Models\MissionQuestionTranslation;
use App\Models\QuestionOptions;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\SubjectTranslation;
use App\Models\TopicTranslation;

class LocaleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:set-locale';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $movies = Movie::all();
        $levelTranslations = LevelTranslation::all();
        $missions = Mission::all();
        $missionImages = MissionImage::all();
        $missionQuestionTranslations = MissionQuestionTranslation::all();
        $questionOptions = QuestionOptions::all();
        $quizs = Quiz::all();
        $quizQuestions = QuizQuestion::all();
        $subjectTranslations = SubjectTranslation::all();
        $topicTranslations = TopicTranslation::all();

        foreach ($movies as $movie) {
            if ($movie->locale == 'hn' || $movie->locale == 'Hn') {
                $movie->locale = 'Hi';
            }
            $movie->locale = ucwords(strtolower($movie->locale));
            $movie->save();
        }
        foreach ($levelTranslations as $levelTranslation) {
            if ($levelTranslation->locale == 'hn' || $levelTranslation->locale == 'Hn') {
                $levelTranslation->locale = 'Hi';
            }
            $levelTranslation->locale = ucwords(strtolower($levelTranslation->locale));
            $levelTranslation->save();
        }
        foreach ($missions as $mission) {
            if ($mission->locale == 'hn' || $mission->locale == 'Hn') {
                $mission->locale = 'Hi';
            }
            $mission->locale = ucwords(strtolower($mission->locale));
            $mission->save();
        }
        foreach ($missionImages as $missionImage) {
            if ($missionImage->locale == 'hn' || $missionImage->locale == 'Hn') {
                $missionImage->locale = 'Hi';
            }
            $missionImage->locale = ucwords(strtolower($missionImage->locale));
            $missionImage->save();
        }
        foreach ($missionQuestionTranslations as $missionQuestionTranslation) {
            if ($missionQuestionTranslation->locale == 'hn' || $missionQuestionTranslation->locale == 'Hn') {
                $missionQuestionTranslation->locale = 'Hi';
            }
            $missionQuestionTranslation->locale = ucwords(strtolower($missionQuestionTranslation->locale));
            $missionQuestionTranslation->save();
        }
        foreach ($questionOptions as $questionOption) {
            if ($questionOption->locale == 'hn' || $questionOption->locale == 'Hn') {
                $questionOption->locale = 'Hi';
            }
            $questionOption->locale = ucwords(strtolower($questionOption->locale));
            $questionOption->save();
        }
        foreach ($quizs as $quiz) {
            if ($quiz->locale == 'hn' || $quiz->locale == 'Hn') {
                $quiz->locale = 'Hi';
            }
            $quiz->locale = ucwords(strtolower($quiz->locale));
            $quiz->save();
        }
        foreach ($quizQuestions as $quizQuestion) {
            if ($quizQuestion->locale == 'hn' || $quizQuestion->locale == 'Hn') {
                $quizQuestion->locale = 'Hi';
            }
            $quizQuestion->locale = ucwords(strtolower($quizQuestion->locale));
            $quizQuestion->save();
        }
        foreach ($subjectTranslations as $subjectTranslation) {
            if ($subjectTranslation->locale == 'hn' || $subjectTranslation->locale == 'Hn') {
                $subjectTranslation->locale = 'Hi';
            }
            $subjectTranslation->locale = ucwords(strtolower($subjectTranslation->locale));
            $subjectTranslation->save();
        }
        foreach ($topicTranslations as $topicTranslation) {
            if ($topicTranslation->locale) {
                if ($topicTranslation->locale == 'hn' || $topicTranslation->locale == 'Hn') {
                    $topicTranslation->locale = 'Hi';
                }
                $topicTranslation->locale = ucwords(strtolower($topicTranslation->locale));
                $topicTranslation->save();
            }
        }

        return 'locale is set successfully';
    }
}
