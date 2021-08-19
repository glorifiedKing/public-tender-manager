<?php

namespace App\Jobs;

use App\Imports\ContractsImport;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class ImportJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $uploadFile;
    public $cacheKey;
    public $userId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($uploadFile,$cacheKey,$userId)
    {
        $this->uploadFile = $uploadFile;
        $this->cacheKey = $cacheKey;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Excel::import(new ContractsImport($this->cacheKey,$this->userId), $this->uploadFile); 
    }
}
