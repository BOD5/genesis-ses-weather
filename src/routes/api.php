<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WeatherController;
use App\Http\Controllers\Api\SubscriptionController;

Route::get('/weather', [WeatherController::class, 'getWeather'])->name('api.weather.get');

Route::post('/subscribe', [SubscriptionController::class, 'subscribe'])->name('api.subscribe');
Route::get('/confirm/{token}', [SubscriptionController::class, 'confirmSubscription'])->name('api.subscribe.confirm');
Route::get('/unsubscribe/{token}', [SubscriptionController::class, 'unsubscribe'])->name('api.subscribe.unsubscribe');
