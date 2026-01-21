<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;

class TicketCommentController extends Controller
{
    public function store(Ticket $ticket, StoreCommentRequest $request): RedirectResponse
    {
        $this->authorize('create', [Comment::class, $ticket]);

        Comment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'body' => $request->validated()['body'],
        ]);

        return back()->with('toast', [
            'type' => 'success',
            'message' => 'Commentaire ajoute.',
        ]);
    }
}
