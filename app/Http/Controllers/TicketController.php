<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketStatusRequest;
use App\Models\Ticket;
use App\Models\TicketStatusChange;
use App\Notifications\TicketCreatedNotification;
use App\Notifications\TicketStatusChangedNotification;
use App\Services\AssignmentService;
use App\Services\TicketQueryService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request, TicketQueryService $ticketQueryService): View
    {
        $filters = $ticketQueryService->filtersFromRequest($request);
        $query = $ticketQueryService->buildQuery($request->user(), $filters)->with('assignee');
        $tickets = $query->paginate(10)->withQueryString();

        $categoryOptions = Ticket::query()
            ->when(!$request->user()->isAgent() && !$request->user()->isAdmin(), function ($builder) use ($request) {
                $builder->where('user_id', $request->user()->id);
            })
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $statusOptions = collect(config('ticket.status', []))
            ->mapWithKeys(fn ($item, $key) => [$key => $item['label'] ?? $key])
            ->all();

        $priorityOptions = collect(config('ticket.priority', []))
            ->mapWithKeys(fn ($item, $key) => [$key => $item['label'] ?? $key])
            ->all();

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

    public function store(StoreTicketRequest $request, AssignmentService $assignmentService): RedirectResponse
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

        $assignedAgent = $assignmentService->assignTicketIfNeeded($ticket);

        if ($ticket->assigned_to) {
            $ticket->loadMissing('assignee');
            if ($ticket->assignee) {
                $ticket->assignee->notify(new TicketCreatedNotification($ticket, true));
            }
        }

        $toastMessage = $assignedAgent
            ? 'Ticket cree avec succes. Assigne a ' . $assignedAgent->name . '.'
            : 'Ticket cree avec succes. Aucun agent disponible, ticket non assigne.';

        return redirect()
            ->route('dashboard')
            ->with('toast', [
                'type' => 'success',
                'message' => $toastMessage,
            ]);
    }
}
