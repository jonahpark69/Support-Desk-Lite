<x-app-layout>
    @php
        $statusLabels = [
            'open' => 'Ouvert',
            'in_progress' => 'En cours',
            'resolved' => 'Resolu',
            'closed' => 'Ferme',
        ];

        $priorityLabels = [
            'low' => 'Faible',
            'normal' => 'Normal',
            'high' => 'Eleve',
            'urgent' => 'Urgent',
        ];
    @endphp

    <div class="container" style="display: grid; gap: var(--space-6);">
        <div class="ticket-header">
            <div>
                <h1 class="page-title">Ticket #{{ $ticket->id }}</h1>
                <p class="page-subtitle">{{ $ticket->title }}</p>
            </div>

            <div style="display: flex; flex-wrap: wrap; gap: var(--space-2);">
                <span class="pill pill--status">{{ $statusLabels[$ticket->status] ?? $ticket->status }}</span>
                <span class="pill pill--priority">{{ $priorityLabels[$ticket->priority] ?? $ticket->priority }}</span>
                @if ($ticket->category)
                    <span class="pill pill--category">{{ $ticket->category }}</span>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card__body">
                <div>
                    <h2 class="page-title" style="font-size: 1.1rem;">Description</h2>
                    <p class="page-subtitle">
                        {{ $ticket->description ?: 'Aucune description' }}
                    </p>
                </div>

                <div class="meta">
                    <div class="meta__row">
                        <span>Cree le {{ $ticket->created_at->format('d/m/Y H:i') }}</span>
                        <span>Auteur: {{ $ticket->user->name }}</span>
                    </div>
                    <div class="meta__row">
                        <span>Assigne a {{ $ticket->assignee?->name ?? 'Non assigne' }}</span>
                    </div>
                    @if ($ticket->resolved_at)
                        <div class="meta__row">
                            <span>Resolu le {{ $ticket->resolved_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                </div>

                @if (auth()->user()->isAgent())
                    <div>
                        <h2 class="page-title" style="font-size: 1.1rem;">Actions agent</h2>
                        <div class="action-bar" style="margin-top: var(--space-3);">
                            <form method="POST" action="{{ route('tickets.assign', $ticket) }}">
                                @csrf
                                @method('PATCH')
                                @if ($ticket->assigned_to === auth()->id())
                                    <button class="btn btn--ghost" type="submit" disabled>Deja assigne</button>
                                @else
                                    <button class="btn btn--ghost" type="submit">M'assigner</button>
                                @endif
                            </form>

                            <form method="POST" action="{{ route('tickets.status', $ticket) }}" class="action-bar">
                                @csrf
                                @method('PATCH')
                                <div>
                                    <label class="form-label" for="status">Changer statut</label>
                                    <select class="input" id="status" name="status">
                                        @foreach (['open' => 'Ouvert', 'in_progress' => 'En cours', 'resolved' => 'Resolu', 'closed' => 'Ferme'] as $value => $label)
                                            <option value="{{ $value }}" @selected($ticket->status === $value)>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <button class="btn btn--primary" type="submit">Mettre a jour</button>
                            </form>
                        </div>
                    </div>
                @endif

                <div style="display: flex; gap: var(--space-3); flex-wrap: wrap;">
                    <a class="btn" href="{{ route('tickets.index') }}">Retour aux tickets</a>
                    <a class="btn btn--primary" href="{{ route('tickets.create') }}">Creer un ticket</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
