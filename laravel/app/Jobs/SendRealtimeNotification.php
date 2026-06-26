<?php

namespace App\Jobs;

use App\Models\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class SendRealtimeNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $endpoint, public Notification $payload)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        Http::post(
            config('services.realtime.url') . $this->endpoint,
            $this->payload
        );
    }
    public function failed(Throwable $e): void
    {
        Log::error('Notification failed', [
            'notif_id' => $this->payload->user_id,
            'error' => $e->getMessage(),
        ]);
    }
}
