<?php

return [
    'status' => [
        'open' => [
            'label' => 'A traiter',
            'classes' => 'bg-amber-50 text-amber-700 ring-amber-200',
        ],
        'in_progress' => [
            'label' => 'En cours',
            'classes' => 'bg-blue-50 text-blue-700 ring-blue-200',
        ],
        'resolved' => [
            'label' => 'Resolu',
            'classes' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        ],
        'closed' => [
            'label' => 'Ferme',
            'classes' => 'bg-slate-100 text-slate-700 ring-slate-200',
        ],
    ],
    'priority' => [
        'low' => [
            'label' => 'Faible',
            'classes' => 'bg-slate-100 text-slate-700 ring-slate-200',
        ],
        'normal' => [
            'label' => 'Normale',
            'classes' => 'bg-sky-50 text-sky-700 ring-sky-200',
        ],
        'high' => [
            'label' => 'Haute',
            'classes' => 'bg-orange-50 text-orange-700 ring-orange-200',
        ],
        'urgent' => [
            'label' => 'Urgente',
            'classes' => 'bg-rose-50 text-rose-700 ring-rose-200',
        ],
    ],
];
