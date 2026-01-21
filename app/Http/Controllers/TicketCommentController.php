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

        $rawBody = $request->validated()['body'];
        $cleanBody = strip_tags($rawBody);
        $cleanBody = str_replace(["\r\n", "\r"], "\n", $cleanBody);
        $cleanBody = preg_replace('/[ \t]+/', ' ', $cleanBody);
        $cleanBody = preg_replace('/\n{3,}/', "\n\n", $cleanBody);
        $cleanBody = trim($cleanBody ?? '');

        if ($cleanBody === '') {
            return back()
                ->withErrors(['body' => 'Commentaire invalide.'])
                ->withInput()
                ->with('toast', [
                    'type' => 'error',
                    'message' => 'Commentaire invalide.',
                ]);
        }

        Comment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'body' => $cleanBody,
        ]);

        return back()->with('toast', [
            'type' => 'success',
            'message' => 'Commentaire ajoute.',
        ]);
    }
}
