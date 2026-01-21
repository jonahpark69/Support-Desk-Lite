<x-app-layout>
    @php
        $hasActiveFilters = collect($filters)
            ->except('sort')
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->isNotEmpty();
    @endphp

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

        <div class="ticket-meta" style="justify-content: space-between; align-items: center;">
            <span>{{ $tickets->total() }} resultats</span>
            @if (auth()->user()->isAdmin() || auth()->user()->isAgent())
                <a
                    class="btn btn--ghost"
                    href="{{ route('tickets.export.csv', request()->except('page')) }}"
                >
                    Exporter CSV
                </a>
            @endif
        </div>

        <div
            x-data="{ loading: true }"
            x-init="setTimeout(() => { loading = false }, 250)"
            :aria-busy="loading.toString()"
            class="relative"
        >
            <div x-show="loading" x-cloak aria-hidden="true" class="pointer-events-none absolute inset-0 z-10 space-y-4">
                @for ($i = 0; $i < 3; $i++)
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="space-y-3">
                            <x-skeleton class="h-5 w-1/2" />
                            <x-skeleton class="h-4 w-2/3" />
                            <div class="flex flex-wrap gap-2">
                                <x-skeleton class="h-6 w-20" rounded="rounded-full" />
                                <x-skeleton class="h-6 w-20" rounded="rounded-full" />
                                <x-skeleton class="h-6 w-24" rounded="rounded-full" />
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <x-skeleton class="h-4 w-32" />
                                <x-skeleton class="h-8 w-20" rounded="rounded-full" />
                            </div>
                        </div>
                    </div>
                @endfor
            </div>

            <div class="space-y-6">
                @if ($tickets->isEmpty())
                    @if ($hasActiveFilters)
                        <x-empty-state
                            title="Aucun resultat"
                            message="Aucun resultat pour ces filtres."
                        >
                            <x-slot:icon>
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z"/>
                                </svg>
                            </x-slot:icon>
                            <x-slot:actions>
                                <a
                                    href="{{ route('tickets.index') }}"
                                    class="inline-flex items-center rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-400"
                                >
                                    Reinitialiser
                                </a>
                            </x-slot:actions>
                        </x-empty-state>
                    @else
                        <x-empty-state
                            title="Aucun ticket"
                            message="Aucun ticket pour l'instant."
                        >
                            <x-slot:actions>
                                <a
                                    href="{{ route('tickets.create') }}"
                                    class="inline-flex items-center rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-900"
                                >
                                    Creer un ticket
                                </a>
                            </x-slot:actions>
                        </x-empty-state>
                    @endif
                @else
                    <div class="ticket-list" :class="loading ? 'opacity-60' : ''">
                        @foreach ($tickets as $ticket)
                            <div class="card ticket-item">
                                <div>
                                    <h2 class="page-title" style="font-size: 1.2rem;">{{ $ticket->title }}</h2>
                                    @if ($ticket->description)
                                        <p class="page-subtitle">{{ \Illuminate\Support\Str::limit($ticket->description, 140) }}</p>
                                    @endif
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    <x-badge type="status" :value="$ticket->status" />
                                    <x-badge type="priority" :value="$ticket->priority" />
                                    @if ($ticket->category)
                                        <x-badge type="category" :value="$ticket->category" />
                                    @endif
                                </div>

                                <div class="ticket-meta">
                                    <span>Cree le {{ $ticket->created_at->format('d/m/Y') }}</span>
                                    @if ($ticket->assignee)
                                        <span>Assigne a {{ $ticket->assignee->name }}</span>
                                    @endif
                                </div>

                                <div>
                                    <a class="btn" href="{{ route('tickets.show', $ticket) }}">Voir</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div>
            {{ $tickets->links() }}
        </div>
    </div>
</x-app-layout>
