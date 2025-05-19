<?php

namespace App\Services;

use App\Models\WeatherHistory;
use Illuminate\Support\Carbon;
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
            $location = $response->json()['location'];
            $this->saveWeatherHistory($current, $location);
            return [
                'temperature' => $current['temp_c'],
                'humidity' => $current['humidity'],
                'description' => $current['condition']['text'],
                'icon' => $current['condition']['icon'],
            ];
        } elseif ($response->status() === 400 || $response->status() === 404) {
            $errorMsg = $response->json()['error']['message'] ?? 'City not found or invalid request';
            Log::warning("WeatherAPI: City '{$city}' not found or invalid request.", ['response' => $response->body()]);
            return $this->getLatestWeatherFromHistory($city, $errorMsg, $response->status());
        } else {
            Log::error("Failed to fetch weather for city {$city} from API. Status: {$response->status()}", [
                'response' => $response->body()
            ]);
            return $this->getLatestWeatherFromHistory($city, 'Weather API service unavailable', $response->status());
        }
    }

    protected function saveWeatherHistory($weather, $location)
    {
        $weatherData = [
            'city_name_from_api' => $location['name'] . (isset($location['region']) && !empty($location['region']) ? ', ' . $location['region'] : '') . ', ' . $location['country'],
            'temperature' => $weather['temp_c'],
            'humidity' => $weather['humidity'],
            'description' => $weather['condition']['text'],
            'icon' => $weather['condition']['icon'],
            'recorded_at' => Carbon::parse($weather['last_updated_epoch'])->toDateTimeString(),
        ];

        WeatherHistory::create([
            'city' => $location['name'],
            'temperature' => $weatherData['temperature'],
            'humidity' => $weatherData['humidity'],
            'description' => $weatherData['description'],
            'icon' => $weatherData['icon'],
            'recorded_at' => $weatherData['recorded_at'],
        ]);

        Log::info("Weather data fetched and saved for city: {$location['name']}");
    }

    protected function getLatestWeatherFromHistory(string $requestedCity, string $apiErrorReason, ?int $apiStatusCode = 503): ?array
    {
        Log::info("Attempting to fetch latest weather for city '{$requestedCity}' from history due to API issue: {$apiErrorReason}");
        $latestHistory = WeatherHistory::where('city', $requestedCity)
            ->orderByDesc('recorded_at')
            ->first();

        if ($latestHistory) {
            Log::info("Found weather data in history for city '{$requestedCity}' recorded at {$latestHistory->recorded_at}.");
            return [
                'city_name_from_api' => $latestHistory->raw_data['location']['name'] . ', ' . $latestHistory->raw_data['location']['country'],
                'temperature' => $latestHistory->temperature,
                'humidity' => $latestHistory->humidity,
                'description' => $latestHistory->description,
                'icon' => $latestHistory->icon,
                'raw_data' => $latestHistory->raw_data,
                'recorded_at' => $latestHistory->recorded_at->toDateTimeString(),
                'from_history' => true,
                'api_error' => $apiErrorReason,
                'api_status_code' => $apiStatusCode
            ];
        }

        Log::warning("No weather data in history found for city '{$requestedCity}'.");
        return ['error' => $apiErrorReason . '. No historical data available.', 'status' => $apiStatusCode];
    }
}
