<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;

class SubscriptionPageController extends Controller
{
    public function showSubscriptionForm()
    {
        return view('subscribe');
    }

    public function handleConfirmation(Request $request, $token)
    {
        if (! $request->hasValidSignature()) {
            return view('subscription_status', ['status' => 'error', 'message' => 'Недійсне або застаріле посилання для підтвердження.']);
        }
        $subscription = Subscription::where('confirmation_token', $token)->first();

        if (!$subscription) {
            return view('subscription_status', ['status' => 'error', 'message' => 'Недійсний або застарілий токен підтвердження.']);
        }

        if ($subscription->isConfirmed()) {
            return view('subscription_status', ['status' => 'info', 'message' => 'Вашу підписку вже було підтверджено.']);
        }

        $subscription->confirmed_at = now();
        $subscription->confirmation_token = null;
        $subscription->save();

        return view('subscription_status', ['status' => 'success', 'message' => 'Вашу підписку успішно підтверджено!']);
    }

    public function handleUnsubscribe(Request $request, $token)
    {
        $subscription = Subscription::where('unsubscribe_token', $token)->first();

        if (!$subscription) {
            return view('subscription_status', ['status' => 'error', 'message' => 'Недійсний токен для відписки або підписку вже видалено.']);
        }

        $subscription->delete();

        return view('subscription_status', ['status' => 'success', 'message' => 'Ви успішно відписалися від оновлень погоди.']);
    }
}
