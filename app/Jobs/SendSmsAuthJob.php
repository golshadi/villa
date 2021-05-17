<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Kavenegar;
use Illuminate\Support\Facades\Log;

class SendSmsAuthJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $phone_number;
    protected $code;

    public function __construct($phone_number, $code)
    {
        $this->phone_number = $phone_number;
        $this->code = $code;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Kavenegar::VerifyLookup($this->phone_number,$this->code,'','','verify');
        Log::alert("55555555".time());
    }

    public function failed()
    {
        Log::emergency('Job failed!');
    }
}