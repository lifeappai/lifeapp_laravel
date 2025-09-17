<?php

namespace App\Jobs;

use App\Exports\UsersExport;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class UserExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    protected $uuid;

    /**
     * Create a new job instance.
     *
     * @param $uuid
     * @param $data
     */

    public function __construct($uuid, $data)
    {
        $this->data = $data;

        $this->uuid = $uuid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Cache::put($this->uuid, [
            'status' => 'in-progress'
        ]);

        Log::info("Export Users Inprogress");
        $fileName = "users-" . time() . ".xlsx";
        Excel::store(new UsersExport($this->data), 'exports/' . $fileName);

        Cache::put($this->uuid, json_encode([
            'status' => 'completed',
            'path' => "exports/" . $fileName,
        ]));
        Log::info("Export Users completed: " . $fileName);
        return 0;
    }
}
