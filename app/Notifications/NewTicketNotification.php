<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTicketNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly Ticket $ticket) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $client = $this->ticket->client;
        $url    = url("/tickets/{$this->ticket->id}");

        return (new MailMessage)
            ->subject("[{$this->ticket->ticket_number}] New ticket from {$client->name}")
            ->greeting("New support ticket")
            ->line("**{$this->ticket->subject}**")
            ->line("Client: {$client->name}")
            ->line("Priority: " . ucfirst($this->ticket->priority))
            ->line("Type: " . ucfirst($this->ticket->type))
            ->action('View Ticket', $url)
            ->line($this->ticket->description ?? '');
    }
}
