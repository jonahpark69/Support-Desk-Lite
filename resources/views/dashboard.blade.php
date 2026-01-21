<x-app-layout>
    <style>
        @media (min-width: 1024px) {
            .stat-grid--agent {
                grid-template-columns: repeat(5, minmax(0, 1fr));
            }
        }
    </style>
    <div class="container" style="display: grid; gap: var(--space-6);">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">Apercu rapide des tickets et priorites du jour.</p>
        </div>

        @if (!empty($statCards))
            <div @class(['stat-grid', 'stat-grid--agent' => auth()->user()->isAgent()])>
                @foreach ($statCards as $card)
                    <a class="card stat-card" href="{{ $card['href'] }}">
                        <p class="page-subtitle" style="margin-top: 0;">{{ $card['title'] }}</p>
                        <div class="stat">
                            <span>{{ $card['value'] }}</span>
                        </div>
                        <p class="page-subtitle" style="margin-top: var(--space-2);">
                            {{ $card['subtitle'] }}
                        </p>
                    </a>
                @endforeach
            </div>
        @endif

        <div class="card">
            <h2 class="page-title">Actions rapides</h2>
            <p class="page-subtitle">Creez ou parcourez vos tickets en un clic.</p>
            <div class="grid" style="margin-top: var(--space-4);">
                <a class="btn btn--primary" href="{{ route('tickets.create') }}">Creer un ticket</a>
                <a class="btn" href="{{ route('tickets.index') }}">Voir tous les tickets</a>
            </div>
        </div>
    </div>
</x-app-layout>
