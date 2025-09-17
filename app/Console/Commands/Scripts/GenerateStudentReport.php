<?php

namespace App\Console\Commands\Scripts;

use App\Enums\UserType;
use App\Exports\StudentExport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class GenerateStudentReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export-students';

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
        $fileName = "students-" . Carbon::now()->toDateString() . "-2.csv";

        $columns = array(
            'Sr No.', 'Student Name', 'District Name', 'Block Name', 'Cluster Name', 'School Name', 'City', 'Grade', 'Mission Completed', 'Register Date'
        );

        $file = fopen(storage_path("exports/{$fileName}"), 'w');

        fputcsv($file, $columns);

        $i = 1;

        User::where('state', 'Chhattisgarh')
            ->where('created_at', '>', '2024-02-12')
            ->whereHas('school', function ($query) {
                $query->where('state', 'Chhattisgarh')
                    ->where('type', UserType::Student);
            })->orderBy("id", "asc")->chunk(500, function ($users) use ($file, &$i) {

            $this->line("fetched: $i - " .  ($i + 500));

            foreach ($users as $user) {
                fputcsv($file, [
                    $i,
                    $user->name ?? '-',
                    $user->school ? $user->school->district : '-',
                    $user->school ? $user->school->block : '-',
                    $user->school ? $user->school->cluster : '-',
                    $user->school ? $user->school->name : '-',
                    $user->city,
                    $user->laGrade->name ?? '',
                    $user->laMissionCompletes->count() ?? '0',
                    $user->created_at ? $user->created_at->format('d-m-Y') : '-',
                ]);
                $i++;
            }

            return true;
        });

        fclose($file);

        $this->line($fileName);
        return 0;
    }
}
