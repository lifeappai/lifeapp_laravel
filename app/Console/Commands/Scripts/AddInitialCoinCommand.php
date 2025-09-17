<?php

namespace App\Console\Commands\Scripts;

use App\Models\CoinTransaction;
use App\Models\User;
use Illuminate\Console\Command;

class AddInitialCoinCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coins:add-initial';

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

        User::orderBy('id')->chunk(50, function ($users) {
            $this->line("User count: " . $users->count());

            foreach ($users as $user) {

                $points = $user->points();

                if (isset($points['total_heart_points']) && $points['total_heart_points']) {
                    $this->addTransaction($user->id, $points['total_heart_points'], CoinTransaction::TYPE_HEART)
                        ->setObject($user)
                        ->save();
                }

                if (isset($points['total_brain_points']) && $points['total_brain_points']) {
                    $this->addTransaction($user->id, $points['total_brain_points'], CoinTransaction::TYPE_BRAIN)
                        ->setObject($user)
                        ->save();
                }
            }
            return true;
        });
        return 0;
    }

    /**
     * @param $userId
     * @param $amount
     * @param $type
     * @return CoinTransaction
     */
    public function addTransaction($userId, $amount, $type)
    {
        return new CoinTransaction([
            'user_id' => $userId,
            'amount' => $amount,
            'type' => $type,
        ]);
    }
}
