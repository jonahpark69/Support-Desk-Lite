<?php

namespace App\Http\Controllers\Tickets;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class TakeTicketController extends Controller
{
    public function __invoke(Ticket $ticket): RedirectResponse
    {
        $this->authorize('take', $ticket);

        $user = request()->user();

        DB::transaction(function () use ($ticket, $user) {
            $ticket->assigned_to = $user->id;

            if ($ticket->status === Ticket::STATUS_OPEN) {
                $ticket->status = Ticket::STATUS_IN_PROGRESS;
                $ticket->resolved_at = null;
            }

            $ticket->save();
        });

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('toast', [
                'type' => 'success',
                'message' => 'Ticket pris en charge.',
            ]);
    }
}
