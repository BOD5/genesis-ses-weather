<?php

namespace App\Mail;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeatherUpdateMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public array $weatherData;
    public Subscription $subscription;

    /**
     * Create a new message instance.
     */
    public function __construct(Subscription $subscription, array $weatherData)
    {
        $this->subscription = $subscription;
        $this->weatherData = $weatherData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Оновлення погоди для міста ' . $this->subscription->city,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.weather.update_html',
            with: [
                'city' => $this->subscription->city,
                'temperature' => $this->weatherData['temperature'] ?? 'N/A',
                'humidity' => $this->weatherData['humidity'] ?? 'N/A',
                'description' => $this->weatherData['description'] ?? 'Дані недоступні',
                'icon' => $this->weatherData['icon'] ?? null,
                'unsubscribeUrl' => $this->subscription->unsubscribe_token ? route('subscription.unsubscribe', ['token' => $this->subscription->unsubscribe_token]) : '#',
                'appName' => config('app.name'),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
