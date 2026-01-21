<x-app-layout>
    <div class="container" style="display: grid; gap: var(--space-6);">
        <div>
            <h1 class="page-title">Créer un ticket</h1>
            <p class="page-subtitle">Décrivez votre demande, un agent vous répondra rapidement.</p>
        </div>

        <div class="card">
            <div class="card__body">
                @if ($errors->any())
                    <div class="form-errors">
                        <p class="page-subtitle" style="margin: 0 0 var(--space-2);">
                            Veuillez corriger les erreurs ci-dessous.
                        </p>
                        <ul style="margin: 0; padding-left: 1.1rem;">
                            @foreach ($errors->all() as $error)
                                <li class="form-error" style="margin-top: 0;">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('tickets.store') }}" style="display: grid; gap: var(--space-4);">
                    @csrf

                    <div>
                        <label class="form-label" for="title">Titre</label>
                        <input
                            class="input"
                            id="title"
                            name="title"
                            type="text"
                            value="{{ old('title') }}"
                            required
                            maxlength="140"
                            autocomplete="off"
                        >
                        @error('title')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label" for="priority">Priorité</label>
                        <select class="input" id="priority" name="priority" required>
                            @foreach (config('ticket.priority', []) as $value => $config)
                                <option value="{{ $value }}" @selected(old('priority', 'normal') === $value)>
                                    {{ $config['label'] ?? $value }}
                                </option>
                            @endforeach
                        </select>
                        @error('priority')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label" for="category">Catégorie</label>
                        <input
                            class="input"
                            id="category"
                            name="category"
                            type="text"
                            value="{{ old('category') }}"
                            maxlength="50"
                            autocomplete="off"
                            placeholder="Ex: Bug, Question, Facturation"
                        >
                        @error('category')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label" for="description">Description</label>
                        <textarea
                            class="input"
                            id="description"
                            name="description"
                            rows="5"
                            maxlength="2000"
                            placeholder="Détails du problème, étapes pour reproduire, impact..."
                        >{{ old('description') }}</textarea>
                        @error('description')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div style="display: flex; gap: var(--space-3); flex-wrap: wrap;">
                        <button class="btn btn--primary" type="submit">Créer le ticket</button>
                        <a class="btn" href="{{ route('dashboard') }}">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
