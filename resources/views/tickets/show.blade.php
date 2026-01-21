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

        $formatSize = function (?int $bytes): ?string {
            if (!$bytes) {
                return null;
            }

            $megabytes = $bytes / (1024 * 1024);
            if ($megabytes >= 1) {
                return number_format($megabytes, 1, ',', '') . ' MB';
            }

            return number_format($bytes / 1024, 0, ',', '') . ' KB';
        };
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
                            @can('take', $ticket)
                                <form method="POST" action="{{ route('tickets.take', $ticket) }}">
                                    @csrf
                                    @if ($ticket->assigned_to === auth()->id())
                                        <button class="btn btn--ghost" type="submit" disabled>Deja pris en charge</button>
                                    @else
                                        <button class="btn btn--ghost" type="submit">Prendre en charge</button>
                                    @endif
                                </form>
                            @endcan

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

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-4">
                <h2 class="text-lg font-semibold text-slate-900">Historique</h2>
                <p class="text-sm text-slate-500">Suivi des changements de statut.</p>
            </div>

            @if ($ticket->statusChanges->isEmpty())
                <p class="text-sm text-slate-500">Aucun changement de statut pour l'instant.</p>
            @else
                <ol class="relative border-l border-slate-200 pl-4">
                    @foreach ($ticket->statusChanges as $change)
                        @php
                            $fromLabel = $statusLabels[$change->from_status] ?? $change->from_status;
                            $toLabel = $statusLabels[$change->to_status] ?? $change->to_status;
                            $authorLabel = $change->changedBy?->name ?: $change->changedBy?->email;
                        @endphp
                        <li class="relative pb-6 last:pb-0">
                            <span class="absolute -left-1.5 top-1 h-2.5 w-2.5 rounded-full bg-slate-400"></span>
                            <div class="text-sm font-medium text-slate-900">
                                De {{ $fromLabel }} -> {{ $toLabel }}
                            </div>
                            <div class="mt-1 text-xs text-slate-500">
                                @if ($authorLabel)
                                    <span>Par {{ $authorLabel }}</span>
                                    <span> | </span>
                                @endif
                                <span>{{ $change->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </li>
                    @endforeach
                </ol>
            @endif
        </div>

        <div class="card">
            <div class="card__body">
                <div>
                    <h2 class="page-title" style="font-size: 1.1rem;">Pieces jointes</h2>
                    <p class="page-subtitle">Ajoutez des fichiers utiles pour le suivi.</p>
                </div>

                @if ($ticket->attachments->isEmpty())
                    <p class="page-subtitle">Aucune piece jointe pour le moment.</p>
                @else
                    <div class="comment-list">
                        @foreach ($ticket->attachments as $attachment)
                            @php($sizeLabel = $formatSize($attachment->size))
                            <div class="comment-item">
                                <div class="comment-header">
                                    <div style="display: flex; align-items: center; gap: var(--space-2);">
                                        <span>{{ $attachment->original_name }}</span>
                                        @if ($sizeLabel)
                                            <span class="badge">{{ $sizeLabel }}</span>
                                        @endif
                                    </div>
                                    <span class="comment-date">{{ $attachment->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <div class="comment-body">
                                    Ajoute par {{ $attachment->user->name }}
                                </div>
                                <div class="action-bar" style="margin-top: var(--space-3);">
                                    <a class="btn btn--ghost" href="{{ route('tickets.attachments.download', [$ticket, $attachment]) }}">
                                        Telecharger
                                    </a>
                                    @if (auth()->user()->isAgent() || $attachment->user_id === auth()->id())
                                        <form method="POST" action="{{ route('tickets.attachments.destroy', [$ticket, $attachment]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn--ghost" type="submit" onclick="return confirm('Supprimer cette piece jointe ?')">
                                                Supprimer
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <form
                    method="POST"
                    action="{{ route('tickets.attachments.store', $ticket) }}"
                    enctype="multipart/form-data"
                    style="display: grid; gap: var(--space-3);"
                >
                    @csrf
                    <div>
                        <label class="form-label" for="file">Ajouter une piece jointe</label>
                        <input class="input" type="file" id="file" name="file" required>
                        @error('file')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="action-bar">
                        <button class="btn btn--primary" type="submit">Envoyer</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card__body">
                <div>
                    <h2 class="page-title" style="font-size: 1.1rem;">Commentaires</h2>
                    <p class="page-subtitle">Partagez les details et l'avancement du ticket.</p>
                </div>

                <div class="comment-list">
                    @forelse ($ticket->comments as $comment)
                        <div class="comment-item">
                            <div class="comment-header">
                                <div style="display: flex; align-items: center; gap: var(--space-2);">
                                    <span>{{ $comment->user->name }}</span>
                                    <span class="badge">
                                        {{ $comment->user->isAgent() ? 'Agent' : 'User' }}
                                    </span>
                                </div>
                                <span class="comment-date">{{ $comment->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="comment-body">
                                {{ $comment->body }}
                            </div>
                        </div>
                    @empty
                        <p class="page-subtitle">Aucun commentaire pour le moment.</p>
                    @endforelse
                </div>

                <form method="POST" action="{{ route('tickets.comments.store', $ticket) }}" style="display: grid; gap: var(--space-3);">
                    @csrf
                    <div>
                        <label class="form-label" for="body">Ajouter un commentaire</label>
                        <textarea
                            class="input"
                            id="body"
                            name="body"
                            rows="4"
                            maxlength="2000"
                            placeholder="Votre message..."
                        >{{ old('body') }}</textarea>
                        @error('body')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="action-bar">
                        <button class="btn btn--primary" type="submit">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
