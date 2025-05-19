<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherApiService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('weather.api_key');
        $this->baseUrl = config('weather.base_url');

        if (empty($this->apiKey)) {
            Log::error('Weather API Key is not configured.');
        }
    }

    public function getCurrentWeather(string $city): ?array
    {
        if (empty($this->apiKey)) {
            return null;
        }

        $response = Http::get("{$this->baseUrl}/current.json", [
            'key' => $this->apiKey,
            'q' => $city,
            'aqi' => 'no'
        ]);

        if ($response->successful() && isset($response->json()['current'])) {
            $current = $response->json()['current'];
            return [
                'temperature' => $current['temp_c'],
                'humidity' => $current['humidity'],
                'description' => $current['condition']['text'],
                'icon' => $current['condition']['icon'],
            ];
        } elseif ($response->status() === 400 || $response->status() === 404) {
            Log::warning("WeatherAPI: City '{$city}' not found or invalid request.", ['response' => $response->body()]);
            return ['error' => 'City not found or invalid request', 'status' => $response->status()];
        } else {
            Log::error("Failed to fetch weather for city {$city}", [
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            return null;
        }
    }
}
