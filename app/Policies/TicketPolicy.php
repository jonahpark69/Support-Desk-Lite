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
}
