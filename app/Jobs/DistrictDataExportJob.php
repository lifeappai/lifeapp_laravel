<?php

namespace App\Jobs;

use App\Exports\DistrictDataExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;


class DistrictDataExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $fileName;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info("Export District data In-progress");
        
        Excel::store(new DistrictDataExport(), 'exports/' . $this->fileName);

        Log::info('Export District completed successfully.');
    }
}
