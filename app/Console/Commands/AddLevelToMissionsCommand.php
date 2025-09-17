<?php

namespace App\Console\Commands;

use App\Models\LaLevel;
use App\Models\LaMission;
use Illuminate\Console\Command;

class AddLevelToMissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add-level-to-mission';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to add level to  mission if it is not set yet';

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
        $laMissions = LaMission::whereNull('la_level_id')->get();

        foreach ($laMissions as $mission) {
            $laLevelId = LaLevel::inRandomOrder()->pluck('id')->first();
            $mission->update([
                'la_level_id' => $laLevelId
            ]);
        }
    }
}
