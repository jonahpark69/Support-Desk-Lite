<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(private Ticket $ticket, private bool $forAssignee = false)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = $this->forAssignee
            ? '[Support Desk Lite] Ticket assigné : ' . $this->ticket->title
            : '[Support Desk Lite] Ticket créé : ' . $this->ticket->title;

        $intro = $this->forAssignee
            ? 'Un ticket vous a été assigné.'
            : 'Votre ticket #' . $this->ticket->id . ' a été créé.';

        $message = (new MailMessage())
            ->subject($subject)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line($intro)
            ->line('Titre: ' . $this->ticket->title)
            ->line('Priorite: ' . $this->ticket->priority)
            ->line('Statut: ' . $this->ticket->status);

        if ($this->ticket->category) {
            $message->line('Categorie: ' . $this->ticket->category);
        }

        return $message
            ->action('Voir le ticket', route('tickets.show', $this->ticket))
            ->salutation('Support Desk Lite');
    }
}
