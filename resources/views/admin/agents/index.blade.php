<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Agents</h1>
            <p class="text-sm text-slate-500">Suivi des agents actifs et de leur charge.</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
            <form method="GET" action="{{ route('admin.agents.index') }}" class="flex flex-col gap-4 sm:flex-row sm:items-end">
                <div class="flex-1">
                    <label for="q" class="text-sm font-medium text-slate-700">Recherche</label>
                    <input
                        id="q"
                        name="q"
                        type="text"
                        value="{{ $search }}"
                        placeholder="Nom ou email"
                        class="mt-1 w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-slate-400 focus:ring-slate-400"
                    >
                </div>
                <div class="flex gap-2">
                    <button
                        type="submit"
                        class="inline-flex items-center rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-900"
                    >
                        Rechercher
                    </button>
                    @if ($search !== '')
                        <a
                            href="{{ route('admin.agents.index') }}"
                            class="inline-flex items-center rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-400"
                        >
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        @if ($agents->isEmpty())
            <x-empty-state
                title="Aucun agent"
                message="{{ $search !== '' ? 'Aucun resultat pour cette recherche.' : 'Aucun agent disponible pour le moment.' }}"
            >
                @if ($search !== '')
                    <x-slot:actions>
                        <a
                            href="{{ route('admin.agents.index') }}"
                            class="inline-flex items-center rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-400"
                        >
                            Reinitialiser
                        </a>
                    </x-slot:actions>
                @endif
            </x-empty-state>
        @else
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-4 py-3 sm:px-6">Nom</th>
                                <th class="px-4 py-3 sm:px-6">Email</th>
                                <th class="px-4 py-3 text-center sm:px-6">Tickets assignes</th>
                                <th class="px-4 py-3 text-center sm:px-6">Tickets actifs</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($agents as $agent)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-4 font-medium text-slate-900 sm:px-6">
                                        {{ $agent->name }}
                                    </td>
                                    <td class="px-4 py-4 text-slate-600 sm:px-6">
                                        {{ $agent->email }}
                                    </td>
                                    <td class="px-4 py-4 text-center text-slate-700 sm:px-6">
                                        {{ $agent->assigned_tickets_count }}
                                    </td>
                                    <td class="px-4 py-4 text-center text-slate-700 sm:px-6">
                                        {{ $agent->active_tickets_count }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-slate-200 bg-white px-4 py-3 sm:px-6">
                    {{ $agents->links() }}
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
