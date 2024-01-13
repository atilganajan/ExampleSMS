<?php

namespace App\Jobs;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendBulkSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $messages = Message::where('status', 'pending')->take(500)->get();

            foreach ($messages as $message) {
                // Example: SmsService::send($message);

                $message->update(['status' => 'sent']);
                $message->smsReport()->create([
                    'user_id' => $message->user_id,
                    'message' => $message->message,
                    'number' => rand(1, 1000),
                    'send_time' => now(),
                ]);
            }
        } catch (\Exception $e) {


        }
    }
}
