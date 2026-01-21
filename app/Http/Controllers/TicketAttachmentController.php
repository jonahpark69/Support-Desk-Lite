<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketAttachment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TicketAttachmentController extends Controller
{
    public function store(Ticket $ticket, Request $request): RedirectResponse
    {
        $this->authorize('view', $ticket);

        $validated = $request->validate([
            'file' => ['required', 'file', 'max:10240', 'mimes:pdf,png,jpg,jpeg,webp,txt'],
        ], [
            'file.max' => 'Fichier trop lourd (max 10 Mo).',
            'file.mimes' => 'Format non autorise (pdf, png, jpg, jpeg, webp, txt).',
        ]);

        $file = $validated['file'];
        $path = $file->store('attachments/' . $ticket->id, 'public');

        TicketAttachment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'mime' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ]);

        return back()->with('toast', [
            'type' => 'success',
            'message' => 'Piece jointe envoyee.',
        ]);
    }

    public function download(Ticket $ticket, TicketAttachment $attachment): StreamedResponse
    {
        $this->authorize('view', $ticket);

        if ($attachment->ticket_id !== $ticket->id) {
            abort(404);
        }

        return Storage::disk('public')->download($attachment->path, $attachment->original_name);
    }

    public function destroy(Ticket $ticket, TicketAttachment $attachment): RedirectResponse
    {
        $this->authorize('view', $ticket);

        if ($attachment->ticket_id !== $ticket->id) {
            abort(404);
        }

        $user = auth()->user();
        if ($user->role !== 'agent' && $attachment->user_id !== $user->id) {
            abort(403);
        }

        Storage::disk('public')->delete($attachment->path);
        $attachment->delete();

        return back()->with('toast', [
            'type' => 'success',
            'message' => 'Piece jointe supprimee.',
        ]);
    }
}
