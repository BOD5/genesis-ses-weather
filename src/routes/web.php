<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubscriptionPageController;

Route::get('/subscribe', [SubscriptionPageController::class, 'showSubscriptionForm'])->name('subscription.form');
Route::get('/subscription/confirm/{token}', [SubscriptionPageController::class, 'handleConfirmation'])->name('subscription.confirm');
Route::get('/subscription/unsubscribe/{token}', [SubscriptionPageController::class, 'handleUnsubscribe'])->name('subscription.unsubscribe');

Route::get('/', [SubscriptionPageController::class, 'showSubscriptionForm']);
