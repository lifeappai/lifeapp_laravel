<?php

namespace App\Console\Commands\Scripts;

use App\Models\LaMissionComplete;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExportMissionData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:missions';

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
        $fileName = "missions-" . Carbon::now()->toDateString() . ".csv";

        $submittedMissions = LaMissionComplete::orderBy('created_at', 'desc');
        
        $columns = array(
            'Sr No.', 'Student Name', 'Mobile Number', 'School', 'Grade', 'Address', 'State', 'City', 'dob', 'Mission Name', 'Submitted Date', 'SUBJECT', 'Submit Media', 'ALLOCATED COINS', 'TOTAL COINS', 'status', 'comments'
        );

        $file = fopen(storage_path("exports/{$fileName}"), 'w');

        fputcsv($file, $columns);

        $i = 1;

        $submittedMissions->orderBy("id", "asc")->chunk(500, function ($submissions) use ($file, &$i) {

            foreach ($submissions as $submission) {
                $user = $submission->user;
                $mission = $submission->laMission;

                fputcsv($file, [
                    $i,
                    $user->name,
                    $user->mobile_no,
                    $user->school ? $user->school->name : '',
                    $user->grade,
                    $user->address,
                    $user->state,
                    $user->city,
                    $user->dob ? date("d-m-Y", strtotime($user->dob)) : "-",
                    $mission->title['en'] ?? '',
                    $submission->created_at ? date("d-m-Y", strtotime($submission->created_at)) : "-",
                    $mission->subject->title["en"] ?? '',
                    $submission->media_id,
                    $submission->points ?? '',
                    $mission->points,
                    $submission->approved_at ? "approved" : ($submission->rejected_at ? "rejected" : "pending"),
                    $submission->comments ?? '',
                ]);
                $i++;
            }
        });

        return 0;
    }
}
