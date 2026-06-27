<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceSentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly Invoice $invoice) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $due = $this->invoice->due_date
            ? "due " . $this->invoice->due_date->format('F j, Y')
            : 'payment due upon receipt';

        return (new MailMessage)
            ->subject("Invoice {$this->invoice->invoice_number} — \${$this->invoice->total}")
            ->greeting("Hi {$notifiable->name},")
            ->line("Please find your invoice **{$this->invoice->invoice_number}** for **\${$this->invoice->total}**, {$due}.")
            ->line("If you have any questions about this invoice, don't hesitate to reach out.")
            ->line("Thank you for your continued partnership.");
    }
}
