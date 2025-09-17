<?php

namespace App\Console\Commands\Scripts;

use App\Models\CoinTransaction;
use App\Models\LaQuizGameQuestionAnswer;
use App\Models\LaQuizGameResult;
use App\Models\User;
use Illuminate\Console\Command;

class ReverseCoins extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reverse-coins {--user=}';

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
        $i = 167;
        $query = User::orderBy("id", "desc")->offset(83500);
        if ($this->option("user")) {
            $userId = $this->option("user");
            $query->where("id", $userId);
        }

        $query->chunk(500, function ($users) use (&$i) {
            $f = $users->first();
            $this->line("fetched: {$f->id} - " .  ($i * $users->count()));

            foreach ($users as $user) {
                $coinTransactions = CoinTransaction::where("coinable_type", LaQuizGameResult::class)->where("user_id", $user->id)->get();

                foreach ($coinTransactions as $coinTransaction) {
                    $result = LaQuizGameResult::find($coinTransaction->coinable_id);

                    if ($result) {
                        $userEarnedCoins = LaQuizGameQuestionAnswer::where("la_quiz_game_id", $result->la_quiz_game_id)->sum("coins");
                        $correctAnswers = LaQuizGameQuestionAnswer::where("la_quiz_game_id", $result->la_quiz_game_id)
                            ->where("is_correct", 1)->count();

                        $result->coins = $userEarnedCoins;
                        $result->total_correct_answers = $correctAnswers;
                        $result->save();

                        $coinTransaction->amount = $userEarnedCoins;
                        $coinTransaction->save();
                    }
                }
            }
            $i++;
            return true;
        });

        return 0;
    }
}
