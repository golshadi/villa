<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Ghasedak\GhasedakApi;
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

    public function __construct($phone_number,$code)
    {
        $this->phone_number=$phone_number;
        $this->code=$code;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $api = new GhasedakApi(env('GHASEDAKAPI_KEY'));
        $api->SendSimple(
            $this->phone_number,  // receptor   
            "باسلام کد احراز هویت شما در سایت : '.$this->code.' میباشد.Trapp.ir", // message   
            "3000xxxxx"    // choose a line number from your account  
        );
        // Log::info('sms sent');
    }

    public function failed(){
        Log::emergency('Job failed!');
    }
}
