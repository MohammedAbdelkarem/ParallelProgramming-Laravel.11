<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;


class SendSMSOTPJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $phone_number;
    protected $OTP;

    public function __construct($phone_number, $OTP)
    {
        $this->phone_number = $phone_number;
        $this->OTP = $OTP;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // $twilio = new Client(
        //     config('services.twilio.sid'),
        //     config('services.twilio.token')
        // );

        // try {
        //     $twilio->messages->create(
        //         $this->phone_number, // To
        //         [
        //             'messagingServiceSid' => config('services.twilio.ms_sid'),
        //             'body' => 'welcome to CargoX, your verification code is: ' . $this->OTP
        //         ]
        //     );

        // } catch (\Exception $e) {
        //     Log::info("Error: " . $e->getMessage());
        // }
    }
}