<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        if ($user->isAgent()) {
            $statCards = [
                [
                    'title' => 'Tickets ouverts',
                    'value' => Ticket::where('status', Ticket::STATUS_OPEN)->count(),
                    'subtitle' => 'A traiter',
                    'href' => route('tickets.index', ['status' => Ticket::STATUS_OPEN]),
                ],
                [
                    'title' => 'En cours',
                    'value' => Ticket::where('status', Ticket::STATUS_IN_PROGRESS)->count(),
                    'subtitle' => 'Suivi actif',
                    'href' => route('tickets.index', ['status' => Ticket::STATUS_IN_PROGRESS]),
                ],
                [
                    'title' => 'Resolus',
                    'value' => Ticket::where('status', Ticket::STATUS_RESOLVED)->count(),
                    'subtitle' => 'Fermes avec succes',
                    'href' => route('tickets.index', ['status' => Ticket::STATUS_RESOLVED]),
                ],
                [
                    'title' => 'Non assignes',
                    'value' => Ticket::whereNull('assigned_to')
                        ->where('status', '!=', Ticket::STATUS_RESOLVED)
                        ->count(),
                    'subtitle' => 'A prendre',
                    'href' => route('tickets.index', ['assigned' => 'unassigned']),
                ],
                [
                    'title' => 'Assignes a moi',
                    'value' => Ticket::where('assigned_to', $user->id)
                        ->where('status', '!=', Ticket::STATUS_RESOLVED)
                        ->count(),
                    'subtitle' => 'A traiter en priorite',
                    'href' => route('tickets.index', ['assigned' => 'me']),
                ],
            ];
        } else {
            $baseQuery = Ticket::query()->where('user_id', $user->id);

            $statCards = [
                [
                    'title' => 'Mes tickets',
                    'value' => (clone $baseQuery)->count(),
                    'subtitle' => 'Total',
                    'href' => route('tickets.index'),
                ],
                [
                    'title' => 'Tickets ouverts',
                    'value' => (clone $baseQuery)->where('status', Ticket::STATUS_OPEN)->count(),
                    'subtitle' => 'En attente',
                    'href' => route('tickets.index', ['status' => Ticket::STATUS_OPEN]),
                ],
                [
                    'title' => 'En cours',
                    'value' => (clone $baseQuery)->where('status', Ticket::STATUS_IN_PROGRESS)->count(),
                    'subtitle' => 'En traitement',
                    'href' => route('tickets.index', ['status' => Ticket::STATUS_IN_PROGRESS]),
                ],
                [
                    'title' => 'Resolus',
                    'value' => (clone $baseQuery)->where('status', Ticket::STATUS_RESOLVED)->count(),
                    'subtitle' => 'Finalises',
                    'href' => route('tickets.index', ['status' => Ticket::STATUS_RESOLVED]),
                ],
            ];
        }

        return view('dashboard', [
            'statCards' => $statCards,
        ]);
    }
}
