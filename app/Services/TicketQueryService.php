<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class TicketQueryService
{
    /**
     * @return array<string, string>
     */
    public function filtersFromRequest(Request $request): array
    {
        return [
            'q' => trim($request->input('q', '')),
            'status' => $request->input('status', ''),
            'priority' => $request->input('priority', ''),
            'category' => trim($request->input('category', '')),
            'assigned' => $request->input('assigned', ''),
            'sort' => $request->input('sort', 'new'),
        ];
    }

    /**
     * @param  array<string, string>  $filters
     */
    public function buildQuery(User $user, array $filters): Builder
    {
        $query = Ticket::query();

        if (!$user->isAgent() && !$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        if ($filters['q'] !== '') {
            $search = $filters['q'];
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        if ($filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        if ($filters['priority'] !== '') {
            $query->where('priority', $filters['priority']);
        }

        if ($filters['category'] !== '') {
            $query->where('category', 'like', '%' . $filters['category'] . '%');
        }

        if ($filters['assigned'] !== '' && $user->isAgent()) {
            if ($filters['assigned'] === 'unassigned') {
                $query->whereNull('assigned_to')
                    ->where('status', '!=', Ticket::STATUS_RESOLVED);
            } elseif ($filters['assigned'] === 'me') {
                $query->where('assigned_to', $user->id)
                    ->where('status', '!=', Ticket::STATUS_RESOLVED);
            }
        }

        if ($filters['sort'] === 'old') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query;
    }
}
