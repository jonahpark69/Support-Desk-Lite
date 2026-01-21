<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function view(User $user, Ticket $ticket): bool
    {
        return $user->isAgent() || $ticket->user_id === $user->id;
    }

    public function updateStatus(User $user, Ticket $ticket): bool
    {
        return $user->isAgent();
    }

    public function take(User $user, Ticket $ticket): bool
    {
        if (!$user->isAgent()) {
            return false;
        }

        return $ticket->assigned_to === null || $ticket->assigned_to === $user->id;
    }
}
