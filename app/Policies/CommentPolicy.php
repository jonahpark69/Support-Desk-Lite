<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class CommentPolicy
{
    public function create(User $user, Ticket $ticket): bool
    {
        return $user->isAgent() || $ticket->user_id === $user->id;
    }
}
