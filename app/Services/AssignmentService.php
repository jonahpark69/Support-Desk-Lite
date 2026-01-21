<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\User;

class AssignmentService
{
    public function assignTicketIfNeeded(Ticket $ticket): ?User
    {
        if ($ticket->assigned_to !== null) {
            return null;
        }

        $agent = User::query()
            ->where('role', 'agent')
            ->withCount([
                'assignedTickets as active_tickets_count' => function ($query) {
                    $query->whereIn('status', [
                        Ticket::STATUS_OPEN,
                        Ticket::STATUS_IN_PROGRESS,
                    ]);
                },
            ])
            ->orderBy('active_tickets_count')
            ->orderBy('id')
            ->first();

        if (!$agent) {
            return null;
        }

        $ticket->assigned_to = $agent->id;
        $ticket->save();

        return $agent;
    }
}
