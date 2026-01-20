<x-app-layout>
    <div class="container" style="display: grid; gap: var(--space-6);">
        <div>
            <h1 class="page-title">Tickets</h1>
            <p class="page-subtitle">Suivi et gestion des demandes.</p>
        </div>

        <div class="card">
            <div class="card__body">
                <form method="GET" action="{{ route('tickets.index') }}" class="filters">
                    <div>
                        <label class="form-label" for="q">Recherche</label>
                        <input
                            class="input"
                            id="q"
                            name="q"
                            type="text"
                            value="{{ $filters['q'] }}"
                            placeholder="Titre ou description"
                        >
                    </div>

                    <div>
                        <label class="form-label" for="status">Statut</label>
                        <select class="input" id="status" name="status">
                            <option value="">Tous</option>
                            @foreach ($statusOptions as $value => $label)
                                <option value="{{ $value }}" @selected($filters['status'] === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label" for="priority">Priorite</label>
                        <select class="input" id="priority" name="priority">
                            <option value="">Toutes</option>
                            @foreach ($priorityOptions as $value => $label)
                                <option value="{{ $value }}" @selected($filters['priority'] === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label" for="category">Categorie</label>
                        @if ($categoryOptions->isNotEmpty())
                            <select class="input" id="category" name="category">
                                <option value="">Toutes</option>
                                @foreach ($categoryOptions as $category)
                                    <option value="{{ $category }}" @selected($filters['category'] === $category)>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <input
                                class="input"
                                id="category"
                                name="category"
                                type="text"
                                value="{{ $filters['category'] }}"
                                placeholder="Ex: Bug, Question"
                            >
                        @endif
                    </div>

                    <div>
                        <label class="form-label" for="sort">Tri</label>
                        <select class="input" id="sort" name="sort">
                            @foreach ($sortOptions as $value => $label)
                                <option value="{{ $value }}" @selected($filters['sort'] === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div style="display: flex; gap: var(--space-2); align-items: flex-end;">
                        <button class="btn btn--primary" type="submit">Filtrer</button>
                        <a class="btn" href="{{ route('tickets.index') }}">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="ticket-meta">
            <span>{{ $tickets->total() }} resultats</span>
        </div>

        <div class="ticket-list">
            @forelse ($tickets as $ticket)
                <div class="card ticket-item">
                    <div>
                        <h2 class="page-title" style="font-size: 1.2rem;">{{ $ticket->title }}</h2>
                        @if ($ticket->description)
                            <p class="page-subtitle">{{ \Illuminate\Support\Str::limit($ticket->description, 140) }}</p>
                        @endif
                    </div>

                    <div style="display: flex; flex-wrap: wrap; gap: var(--space-2);">
                        <span class="pill pill--status">{{ $ticket->status }}</span>
                        <span class="pill pill--priority">{{ $ticket->priority }}</span>
                        @if ($ticket->category)
                            <span class="pill pill--category">{{ $ticket->category }}</span>
                        @endif
                    </div>

                    <div class="ticket-meta">
                        <span>Cree le {{ $ticket->created_at->format('d/m/Y') }}</span>
                        @if ($ticket->assignee)
                            <span>Assigne a {{ $ticket->assignee->name }}</span>
                        @endif
                    </div>

                    <div>
                        <button class="btn" type="button" disabled>Voir</button>
                    </div>
                </div>
            @empty
                <div class="card">
                    <p class="page-subtitle">Aucun ticket ne correspond a vos filtres.</p>
                </div>
            @endforelse
        </div>

        <div>
            {{ $tickets->links() }}
        </div>
    </div>
</x-app-layout>
