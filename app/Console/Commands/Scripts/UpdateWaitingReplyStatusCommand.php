<?php

namespace App\Console\Commands\Scripts;

use App\Models\LaQuery;
use Illuminate\Console\Command;

class UpdateWaitingReplyStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:query-status';

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
        LaQuery::latest()->chunk(50, function ($queries) {
            $this->line("Count: " . $queries->count());
            foreach ($queries as $query) {
                $reply = $query->laReplies()->latest()->first();
                if ($reply) {
                    $query->waiting_reply = (int)($reply->user_id == $query->created_by);
                    $query->save();
                }
            }
            return true;
        });
        return 0;
    }
}
