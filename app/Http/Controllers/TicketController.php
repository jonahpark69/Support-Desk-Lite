<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketStatusRequest;
use App\Models\Ticket;
use App\Models\TicketStatusChange;
use App\Notifications\TicketCreatedNotification;
use App\Notifications\TicketStatusChangedNotification;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'q' => $request->input('q', ''),
            'status' => $request->input('status', ''),
            'priority' => $request->input('priority', ''),
            'category' => $request->input('category', ''),
            'assigned' => $request->input('assigned', ''),
            'sort' => $request->input('sort', 'new'),
        ];

        $query = Ticket::query()->with('assignee');

        if (!$request->user()->isAgent()) {
            $query->where('user_id', $request->user()->id);
        }

        if ($filters['q'] !== '') {
            $search = $filters['q'];
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        if ($filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        if ($filters['priority'] !== '') {
            $query->where('priority', $filters['priority']);
        }

        if ($filters['category'] !== '') {
            $query->where('category', 'like', '%' . $filters['category'] . '%');
        }

        if ($filters['assigned'] !== '' && $request->user()->isAgent()) {
            if ($filters['assigned'] === 'unassigned') {
                $query->whereNull('assigned_to')
                    ->where('status', '!=', Ticket::STATUS_RESOLVED);
            } elseif ($filters['assigned'] === 'me') {
                $query->where('assigned_to', $request->user()->id)
                    ->where('status', '!=', Ticket::STATUS_RESOLVED);
            }
        }

        if ($filters['sort'] === 'old') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $tickets = $query->paginate(10)->withQueryString();

        $categoryOptions = Ticket::query()
            ->when(!$request->user()->isAgent(), function ($builder) use ($request) {
                $builder->where('user_id', $request->user()->id);
            })
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $statusOptions = [
            Ticket::STATUS_OPEN => 'Ouvert',
            Ticket::STATUS_IN_PROGRESS => 'En cours',
            Ticket::STATUS_RESOLVED => 'Resolue',
            Ticket::STATUS_CLOSED => 'Ferme',
        ];

        $priorityOptions = [
            Ticket::PRIORITY_LOW => 'Basse',
            Ticket::PRIORITY_NORMAL => 'Normale',
            Ticket::PRIORITY_HIGH => 'Haute',
            Ticket::PRIORITY_URGENT => 'Urgente',
        ];

        $sortOptions = [
            'new' => 'Plus recent',
            'old' => 'Plus ancien',
        ];

        return view('tickets.index', [
            'tickets' => $tickets,
            'filters' => $filters,
            'statusOptions' => $statusOptions,
            'priorityOptions' => $priorityOptions,
            'categoryOptions' => $categoryOptions,
            'sortOptions' => $sortOptions,
        ]);
    }

    public function create(): View
    {
        return view('tickets.create');
    }

    public function show(Ticket $ticket): View
    {
        $this->authorize('view', $ticket);

        $ticket->load(['user', 'assignee', 'comments.user', 'attachments.user', 'statusChanges.changedBy']);

        return view('tickets.show', compact('ticket'));
    }

    public function assignToMe(Ticket $ticket): RedirectResponse
    {
        $user = request()->user();

        if (!$user->isAgent()) {
            abort(403);
        }

        $ticket->assigned_to = $user->id;
        $ticket->save();

        return back()->with('toast', [
            'type' => 'success',
            'message' => 'Ticket assigne.',
        ]);
    }

    public function updateStatus(UpdateTicketStatusRequest $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('updateStatus', $ticket);

        $oldStatus = $ticket->status;
        $status = $request->validated()['status'];
        $changed = $oldStatus !== $status;

        DB::transaction(function () use ($ticket, $status, $changed, $request, $oldStatus) {
            if (!$changed) {
                return;
            }

            $ticket->status = $status;
            $ticket->resolved_at = $status === Ticket::STATUS_RESOLVED ? now() : null;
            $ticket->save();

            TicketStatusChange::create([
                'ticket_id' => $ticket->id,
                'from_status' => $oldStatus,
                'to_status' => $status,
                'changed_by' => $request->user()?->id,
            ]);
        });

        if ($changed) {
            $ticket->user->notify(new TicketStatusChangedNotification($ticket, $oldStatus, $status));
        }

        return back()->with('toast', [
            'type' => 'success',
            'message' => 'Statut mis a jour.',
        ]);
    }

    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $ticket = Ticket::create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status' => Ticket::STATUS_OPEN,
            'priority' => $validated['priority'],
            'category' => $validated['category'] ?? null,
            'assigned_to' => null,
            'resolved_at' => null,
        ]);

        $request->user()->notify(new TicketCreatedNotification($ticket));

        if ($ticket->assigned_to) {
            $ticket->loadMissing('assignee');
            if ($ticket->assignee) {
                $ticket->assignee->notify(new TicketCreatedNotification($ticket, true));
            }
        }

        return redirect()
            ->route('dashboard')
            ->with('toast', [
                'type' => 'success',
                'message' => 'Ticket créé avec succès.',
            ]);
    }
}
