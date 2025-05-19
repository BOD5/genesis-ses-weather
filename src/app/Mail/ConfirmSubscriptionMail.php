<?php

namespace App\Mail;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class ConfirmSubscriptionMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Subscription $subscription;
    public string $confirmationUrl;

    public function __construct(Subscription $subscription)
    {
        Log::info('ConfirmSubscriptionMail constructor called for email: ' . $subscription->email);
        $this->subscription = $subscription;
        try {
            $this->confirmationUrl = URL::temporarySignedRoute(
                'subscription.confirm',
                now()->addHours(24),
                ['token' => $subscription->confirmation_token]
            );
            Log::info('Confirmation URL generated: ' . $this->confirmationUrl);
        } catch (\Exception $e) {
            Log::error('Failed to generate confirmation URL.', ['error' => $e->getMessage()]);
            $this->confirmationUrl = '#error-generating-url'; // Заглушка
        }
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Підтвердіть Вашу підписку на оновлення погоди',
        );
    }

    public function content(): Content
    {
        Log::info('ConfirmSubscriptionMail content/build method called for: ' . $this->subscription->email);

        return new Content(
            view: 'emails.subscription.confirm_html',
            with: [
                'confirmationUrl' => $this->confirmationUrl,
                'subscriberEmail' => $this->subscription->email,
                'city' => $this->subscription->city,
                'appName' => config('app.name'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
