<?php

namespace App\Console\Commands;

use App\Models\PushNotificationCampaign;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ScheduleNotificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:push-notification-campaigns';

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
        $campaigns = PushNotificationCampaign::where('scheduled_date', '<', Carbon::now()->toDateTimeString())
            ->whereNull('scheduled_at')
            ->get();

        foreach ($campaigns as $campaign) {
            $campaign->schedule();
        }

        return 0;
    }
}
