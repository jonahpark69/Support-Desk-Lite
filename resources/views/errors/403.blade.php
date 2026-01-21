<x-app-layout>
    <div class="container" style="display: grid; gap: var(--space-6);">
        <div>
            <h1 class="page-title">Action non autorisee</h1>
            <p class="page-subtitle">Vous n'avez pas les droits pour acceder a cette page.</p>
        </div>

        <div class="card">
            <div class="card__body">
                <p class="page-subtitle">Retournez a votre espace pour continuer.</p>
                <div style="display: flex; gap: var(--space-3); flex-wrap: wrap;">
                    <a class="btn btn--primary" href="{{ route('dashboard') }}">Dashboard</a>
                    <a class="btn" href="{{ route('tickets.index') }}">Tickets</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
