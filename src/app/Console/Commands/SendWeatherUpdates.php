<?php

namespace App\Console\Commands;

use App\Jobs\SendWeatherUpdateEmailJob;
use App\Models\Subscription;
use App\Services\WeatherApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class SendWeatherUpdates extends Command
{
    protected $signature = 'weather:send-updates {--frequency=all : Send updates for a specific frequency (hourly, daily, or all)}';
    protected $description = 'Fetch weather data and dispatch jobs to send updates to subscribers.';

    protected WeatherApiService $weatherApiService;

    public function __construct(WeatherApiService $weatherApiService)
    {
        parent::__construct();
        $this->weatherApiService = $weatherApiService;
    }

    public function handle(): int
    {
        $frequencyOption = $this->option('frequency');
        $this->info("Starting to send weather updates for frequency: {$frequencyOption}...");

        $query = Subscription::whereNotNull('confirmed_at');

        if ($frequencyOption !== 'all') {
            if (!in_array($frequencyOption, ['hourly', 'daily'])) {
                $this->error("Invalid frequency provided. Use 'hourly', 'daily', or 'all'.");
                return Command::FAILURE;
            }
            $query->where('frequency', $frequencyOption);
        }
        $subscriptions = $query->get();

        if ($subscriptions->isEmpty()) {
            $this->info("No active subscriptions found for frequency: {$frequencyOption}.");
            return Command::SUCCESS;
        }

        $this->info("Found {$subscriptions->count()} subscriptions to process for frequency: {$frequencyOption}.");

        $subscriptionsByCity = $subscriptions->groupBy('city');
        $processedEmails = 0;

        foreach ($subscriptionsByCity as $city => $citySubscriptions) {
            $this->info("Fetching weather for city: {$city}...");
            $weatherData = $this->weatherApiService->getCurrentWeather($city);

            if (!$weatherData || isset($weatherData['error'])) {
                $errorMessage = $weatherData['error'] ?? 'Could not retrieve weather data';
                Log::error("Failed to fetch weather for city {$city} in SendWeatherUpdates command: {$errorMessage}");
                $this->error("Failed to fetch weather for city {$city}: {$errorMessage}. Skipping subscriptions for this city.");
                continue;
            }
            $this->info("Weather data received for {$city}: Temp {$weatherData['temperature']}°C, {$weatherData['description']}");

            foreach ($citySubscriptions as $subscription) {
                if ($subscription->frequency === 'daily' && $frequencyOption !== 'daily') {
                    if (Carbon::now()->hour !== 8 && $frequencyOption === 'all') {
                        Log::info("Skipping daily subscription for {$subscription->email} for city {$city} as it's not 8 AM.");
                        continue;
                    }
                }

                SendWeatherUpdateEmailJob::dispatch($subscription, $weatherData)->onQueue('emails');
                $this->comment("Dispatched weather update job for: {$subscription->email} [{$subscription->city} - {$subscription->frequency}]");
                $processedEmails++;
            }
        }

        $this->info("All weather update jobs dispatched. Processed emails: {$processedEmails}.");
        return Command::SUCCESS;
    }
}
