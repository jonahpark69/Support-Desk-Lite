<x-app-layout>
    <div class="container" style="display: grid; gap: var(--space-6);">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">Apercu rapide des tickets et priorites du jour.</p>
        </div>

        <div class="grid">
            <div class="card">
                <p class="page-subtitle">Tickets ouverts</p>
                <div class="stat">
                    <span>12</span>
                    <span class="badge">+3</span>
                </div>
            </div>

            <div class="card">
                <p class="page-subtitle">En cours</p>
                <div class="stat">
                    <span>5</span>
                    <span class="badge">SLA</span>
                </div>
            </div>

            <div class="card">
                <p class="page-subtitle">Resolus</p>
                <div class="stat">
                    <span>128</span>
                    <span class="badge">7j</span>
                </div>
            </div>
        </div>

        <div class="card">
            <h2 class="page-title">Actions rapides</h2>
            <p class="page-subtitle">Creez ou parcourez vos tickets en un clic.</p>
            <div class="grid" style="margin-top: var(--space-4);">
                <a class="btn btn--primary" href="#">Creer un ticket</a>
                <a class="btn" href="#">Voir tous les tickets</a>
            </div>
        </div>
    </div>
</x-app-layout>
