<?php

namespace App\Console\Commands\Scripts;

use App\Enums\UserType;
use App\Models\CronLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExportUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:users';

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

        $fileName = "users-" . Carbon::now()->toDateString() . ".csv";

        $columns = [
            'Sr No.',
            'Student Name',
            'Image',
            'Mobile Number',
            'School',
            'School Code',
            'District Name',
            'Block Name',
            'Cluster Name',
            'Grade',
            'Section',
            'Type',
            'Address',
            'State',
            'City',
            'DOB',
            'Mission Completed',
            'Mission Requested',
            'Quiz',
            'Earn Coins',
            'Quiz Coins',
            'Mission Coins',
            'Coins Redeemed',
            'Product Redeemed',
            'Rating',
            'Register Date'
        ];

        $file = fopen(storage_path("exports/{$fileName}"), 'w');

        fputcsv($file, $columns);

        $i = 1;

        User::orderBy("id", "asc")->chunk(500, function ($users) use ($file, &$i) {

            $imageBaseUrl = "https://media.gappubobo.com/";
            $this->line("fetched: $i - " . ($i + 500));
            Log::info("fetched: $i - " . ($i + 500));

            foreach ($users as $user) {
                $userImage = $user->image_path ? $imageBaseUrl . $user->image_path : '';
                $userType = '-';
                if ($user->type == UserType::Student) {
                    $userType = 'Student';
                } elseif ($user->type == UserType::Teacher) {
                    $userType = 'Teacher';
                } elseif ($user->type == UserType::Mentor) {
                    $userType = 'Mentor';
                }

                fputcsv($file, [
                    $i,
                    $user->name,
                    $userImage,
                    $user->mobile_no,
                    $user->school ? $user->school->name : '',
                    $user->school ? $user->school->code : '',
                    $user->school->district ?? '',
                    $user->school->block ?? '',
                    $user->school->cluster ?? '',
                    $user->laGrade->name ?? '',
                    $user->laSection->name ?? '',
                    $userType,
                    $user->address,
                    $user->state,
                    $user->city,
                    $user->dob ? date("d-m-Y", strtotime($user->dob)) : "-",
                    $user->laMissionApproved()->count(),
                    $user->laMissionRequests()->count(),
                    $user->laQuizGameResults->count(),
                    $user->earn_coins,
                    $user->earnCoinsByType('quiz'),
                    $user->earnCoinsByType('mission'),
                    $user->couponRedeems->sum('coins'),
                    $user->laSubjectCouponCodes->count(),
                    $user->user_rank,
                    $user->created_at ? date("d-m-Y", strtotime($user->created_at)) : "-"
                ]);
                $i++;
            }

            return true;
        });

        fclose($file);

        $log->completed_at = Carbon::now()->toDateTimeString();
        $log->save();

        return 0;
    }
}
