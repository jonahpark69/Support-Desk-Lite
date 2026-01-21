<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgentController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->query('q', ''));

        $query = User::query()
            ->where('role', 'agent')
            ->withCount([
                'assignedTickets as assigned_tickets_count',
                'assignedTickets as active_tickets_count' => function ($builder) {
                    $builder->whereIn('status', [
                        Ticket::STATUS_OPEN,
                        Ticket::STATUS_IN_PROGRESS,
                    ]);
                },
            ]);

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $agents = $query
            ->orderBy('name')
            ->orderBy('email')
            ->paginate(15)
            ->withQueryString();

        return view('admin.agents.index', [
            'agents' => $agents,
            'search' => $search,
        ]);
    }
}
