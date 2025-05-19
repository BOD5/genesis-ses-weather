<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\WeatherApiService;

class WeatherController extends Controller
{
    protected WeatherApiService $weatherApi;

    public function __construct(WeatherApiService $weatherApiService)
    {
        $this->weatherApi = $weatherApiService;
    }

    public function getWeather(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'city' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $city = $request->input('city');
        $weatherData = $this->weatherApi->getCurrentWeather($city);

        if (!$weatherData) {
            return response()->json(['error' => 'Could not retrieve weather data at this time.'], 500);
        }

        if (isset($weatherData['error'])) {
            return response()->json(['error' => $weatherData['error']], $weatherData['status'] ?? 404);
        }
        return response()->json([
            'temperature' => $weatherData['temperature'],
            'humidity' => $weatherData['humidity'],
            'description' => $weatherData['description'],
        ]);
    }
}
