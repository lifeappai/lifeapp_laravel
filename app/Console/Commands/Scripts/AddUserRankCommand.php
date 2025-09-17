<?php

namespace App\Console\Commands\Scripts;

use App\Models\User;
use Illuminate\Console\Command;
use App\Models\CronLog;
use Carbon\Carbon;

class AddUserRankCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set:user-rank';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to set user rank in user table';

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

        $users = User::orderBy('earn_coins', 'DESC')->get();

        foreach ($users as $index => $user) {
            $user->update([
                'user_rank' => $index + 1
            ]);
            $this->line("Set User Rank for: {$user->name} ");
        }

        $log->completed_at = Carbon::now()->toDateTimeString();
        $log->save();
    }
}
