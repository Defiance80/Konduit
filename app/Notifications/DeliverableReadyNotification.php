<?php

namespace App\Notifications;

use App\Models\Deliverable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeliverableReadyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly Deliverable $deliverable) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $project = $this->deliverable->project;
        $url     = url("/client/approvals/{$this->deliverable->id}");

        return (new MailMessage)
            ->subject("Your approval is needed — {$this->deliverable->title}")
            ->greeting("Hi {$notifiable->name},")
            ->line("A deliverable is ready for your review on **{$project->name}**.")
            ->line("**{$this->deliverable->title}**")
            ->action('Review & Approve', $url)
            ->line("Your feedback keeps the project moving — please review at your earliest convenience.");
    }
}
