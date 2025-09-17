<?php

namespace App\Console\Commands\Scripts;

use App\Models\User;
use Illuminate\Console\Command;

class SetUserCoinsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set:user-coins';

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
        if (!$this->confirm("It will reset all points of user. are you sure?")) {
            return 0;
        }

        $users = User::all();

        foreach ($users as $user) {
            $points = $user->points();
            $user->brain_coins = $points['total_brain_points'];
            $user->heart_coins = $points['total_heart_points'];
            $user->save();

            $this->line("Set User: {$user->name} - Brains: {$user->brain_coins} - Heart: {$user->heart_coins}");
        }
        return 0;
    }
}
