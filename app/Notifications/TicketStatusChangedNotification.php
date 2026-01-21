<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private Ticket $ticket,
        private string $from,
        private string $to
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusLabels = [
            Ticket::STATUS_OPEN => 'Ouvert',
            Ticket::STATUS_IN_PROGRESS => 'En cours',
            Ticket::STATUS_RESOLVED => 'Resolue',
            Ticket::STATUS_CLOSED => 'Ferme',
        ];

        $fromLabel = $statusLabels[$this->from] ?? $this->from;
        $toLabel = $statusLabels[$this->to] ?? $this->to;

        $message = (new MailMessage())
            ->subject('[Support Desk Lite] Statut mis a jour : ' . $this->ticket->title)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Le statut de votre ticket #' . $this->ticket->id . ' a change.')
            ->line('Statut: ' . $fromLabel . ' -> ' . $toLabel)
            ->line('Priorite: ' . $this->ticket->priority);

        if ($this->ticket->category) {
            $message->line('Categorie: ' . $this->ticket->category);
        }

        return $message
            ->action('Voir le ticket', route('tickets.show', $this->ticket))
            ->salutation('Support Desk Lite');
    }
}
