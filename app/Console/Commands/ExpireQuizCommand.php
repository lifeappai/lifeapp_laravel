<?php

namespace App\Console\Commands;

use App\Enums\QuizGameStatusEnum;
use App\Models\CronLog;
use App\Models\LaQuizGame;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpireQuizCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expire:quiz';

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
        $log = CronLog::create([
            'command' => $this->signature
        ]);

        $now = Carbon::now()->addMinutes(-25)->toDateString();

        $quizez = LaQuizGame::where('status', QuizGameStatusEnum::PENDING)
            ->where('created_at', '<', $now)
            ->get();

        foreach ($quizez as $quiz) {
            $quiz->status = QuizGameStatusEnum::EXPIRED;
            $quiz->save();
        }

        $log->completed_at = Carbon::now()->toDateTimeString();
        $log->save();

        return 0;
    }
}
