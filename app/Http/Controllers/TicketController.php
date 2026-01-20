<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function create(): View
    {
        return view('tickets.create');
    }

    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Ticket::create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status' => Ticket::STATUS_OPEN,
            'priority' => $validated['priority'],
            'category' => $validated['category'] ?? null,
            'assigned_to' => null,
            'resolved_at' => null,
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Ticket créé avec succès.');
    }
}
