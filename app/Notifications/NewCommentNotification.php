<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewCommentNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Ticket $ticket,
        private readonly Comment $comment,
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
        $commenter = $this->comment->user;
        $snippet = Str::limit($this->comment->body, 140);
        $statusLabel = config('ticket.status.' . $this->ticket->status . '.label', $this->ticket->status);

        return (new MailMessage)
            ->subject('[Ticket #' . $this->ticket->id . '] Nouveau commentaire')
            ->greeting('Bonjour,')
            ->line('Un nouveau commentaire a ete ajoute sur le ticket "' . $this->ticket->title . '".')
            ->line('Statut actuel: ' . $statusLabel . '.')
            ->when($commenter, function (MailMessage $message) use ($commenter) {
                return $message->line('Commentaire de ' . $commenter->name . '.');
            })
            ->line('Extrait: ' . $snippet)
            ->action('Voir le ticket', route('tickets.show', $this->ticket))
            ->line('Merci.');
    }
}
