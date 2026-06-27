<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketAcknowledgementNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Ticket $ticket,
        private readonly string $clientMessage = ''
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = $this->clientMessage ?: "We've received your request and our team will be in touch shortly.";

        return (new MailMessage)
            ->subject("We received your request — {$this->ticket->ticket_number}")
            ->greeting("Hi {$notifiable->name},")
            ->line($message)
            ->line("Your ticket number is **{$this->ticket->ticket_number}**. Keep this for your records.")
            ->line("We'll update you as we make progress.");
    }
}
