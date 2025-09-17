<?php

namespace App\Console\Commands\Scripts;

use App\Enums\StatusEnum;
use App\Models\School;
use Illuminate\Console\Command;

class ImportSchoolCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:school';

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
        $csv = array_map('str_getcsv', file(storage_path("/data/school_district.csv")));

        for ($i = 1; $i <  count($csv); $i++) {
            $item = $csv[$i];
            $schoolCode = $item[3];

            $this->line($i . " ADDED - " . $schoolCode );
            School::where("code", $schoolCode)
                ->update([
                        "district" => $item[0],
                        "block" => $item[1],
                        'cluster' => $item[2],
                    ]);
        }

        return 0;
    }
}
