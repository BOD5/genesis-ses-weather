<?php

namespace App\Jobs;

use App\Mail\WeatherUpdateMail;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWeatherUpdateEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Subscription $subscription;
    public array $weatherData;

    /**
     * Create a new job instance.
     */
    public function __construct(Subscription $subscription, array $weatherData)
    {
        $this->subscription = $subscription;
        $this->weatherData = $weatherData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            if (!$this->subscription->isConfirmed()) {
                Log::info("Skipping weather update for unconfirmed subscription: {$this->subscription->email} for city {$this->subscription->city}");
                return;
            }
            Log::info("Sending weather update to: {$this->subscription->email} for city {$this->subscription->city}");
            Mail::to($this->subscription->email)->send(new WeatherUpdateMail($this->subscription, $this->weatherData));
            Log::info("Weather update email successfully dispatched for: {$this->subscription->email} for city {$this->subscription->city}");
        } catch (\Exception $e) {
            Log::error("Failed to send weather update email to {$this->subscription->email} for city {$this->subscription->city}", [
                'error_message' => $e->getMessage(),
            ]);
            $this->fail($e);
        }
    }
}
