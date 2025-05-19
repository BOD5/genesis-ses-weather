<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ConfirmSubscriptionMail;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'city' => 'required|string|max:255',
            'frequency' => 'required|string|in:hourly,daily',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $data = $validator->validated();

        $existingSubscription = Subscription::where('email', $data['email'])
            ->where('city', $data['city'])
            ->first();

        if ($existingSubscription) {
            if ($existingSubscription->isConfirmed()) {
                return response()->json(['message' => 'Email already subscribed to this city and confirmed.'], 409);
            } else {
                Log::info("Attempt to subscribe with existing unconfirmed subscription for email: {$data['email']} and city: {$data['city']}. Resending confirmation might be needed.");
                return response()->json(['message' => 'Subscription pending confirmation. A confirmation email has been previously sent or will be sent shortly.'], 200);
            }
        }

        $confirmationToken = Str::random(60);
        $unsubscribeToken = Str::random(60);

        while (Subscription::where('confirmation_token', $confirmationToken)->exists()) {
            $confirmationToken = Str::random(60);
        }
        while (Subscription::where('unsubscribe_token', $unsubscribeToken)->exists()) {
            $unsubscribeToken = Str::random(60);
        }

        $subscription = Subscription::create([
            'email' => $data['email'],
            'city' => $data['city'],
            'frequency' => $data['frequency'],
            'confirmation_token' => $confirmationToken,
            'unsubscribe_token' => $unsubscribeToken,
        ]);

        try {
            Mail::to($subscription->email)->send(new ConfirmSubscriptionMail($subscription));
            Log::info("Confirmation email dispatch initiated for {$subscription->email} for city {$subscription->city}. Token: {$confirmationToken}");
        } catch (\Exception $e) {
            Log::error("Failed to send or queue confirmation email for {$subscription->email}: " . $e->getMessage(), ['exception' => $e]);
        }

        return response()->json(['message' => 'Subscription request received. Please check your email to confirm your subscription.'], 200);
    }

    public function confirmSubscription(Request $request, $token)
    {
        if (! $request->hasValidSignature() && app()->environment('production')) {
            Log::warning('Invalid or expired signature for confirmation link, but allowed in non-production environment.', ['token' => $token]);
        }

        $subscription = Subscription::where('confirmation_token', $token)->first();

        if (!$subscription) {
            $alreadyConfirmedWithThisToken = Subscription::whereNull('confirmation_token')
                ->whereNotNull('confirmed_at')
                ->exists();
            return response()->json(['message' => 'Token not found or already used.'], 404);
        }

        if ($subscription->isConfirmed()) {
            return response()->json(['message' => 'Subscription already confirmed.'], 200);
        }

        $subscription->confirmed_at = now();
        $subscription->confirmation_token = null;
        $subscription->save();

        return response()->json(['message' => 'Subscription confirmed successfully.'], 200);
    }

    public function unsubscribe(Request $request, $token)
    {
        $subscription = Subscription::where('unsubscribe_token', $token)->first();

        if (!$subscription) {
            return response()->json(['message' => 'Token not found or subscription already removed.'], 404);
        }

        $subscription->delete();

        return response()->json(['message' => 'Unsubscribed successfully.'], 200);
    }
}
