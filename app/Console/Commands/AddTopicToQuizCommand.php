<?php

namespace App\Console\Commands;

use App\Models\LaQuestion;
use App\Models\LaTopic;
use Illuminate\Console\Command;

class AddTopicToQuizCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add-topic-to-quiz';

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
        $laQuestions = LaQuestion::whereNull('la_topic_id')->get();

        foreach ($laQuestions as $laQuestion) {
            $laTopicId = LaTopic::inRandomOrder()->pluck('id')->first();
            $laQuestion->update([
                'la_topic_id' => $laTopicId
            ]);
        }
    }
}
