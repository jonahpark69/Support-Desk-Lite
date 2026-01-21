<?php

namespace App\Http\Controllers\Tickets;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Services\TicketQueryService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportTicketsCsvController extends Controller
{
    public function __invoke(Request $request, TicketQueryService $ticketQueryService): StreamedResponse
    {
        $user = $request->user();

        if (!$user || (!$user->isAdmin() && !$user->isAgent())) {
            abort(403);
        }

        $filters = $ticketQueryService->filtersFromRequest($request);
        $query = $ticketQueryService->buildQuery($user, $filters)
            ->with(['user', 'assignee']);

        $filename = 'tickets-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                return;
            }

            fwrite($handle, "\xEF\xBB\xBF");

            $headers = [
                'id',
                'title',
                'status_label_fr',
                'priority_label_fr',
                'created_by_email',
                'assigned_to_email',
                'created_at',
                'updated_at',
            ];

            fputcsv($handle, $headers, ';');

            foreach ($query->cursor() as $ticket) {
                $statusLabel = config('ticket.status.' . $ticket->status . '.label', $ticket->status);
                $priorityLabel = config('ticket.priority.' . $ticket->priority . '.label', $ticket->priority);
                $row = [
                    $ticket->id,
                    $ticket->title,
                    $statusLabel,
                    $priorityLabel,
                    $ticket->user?->email ?? '',
                    $ticket->assignee?->email ?? '',
                    $ticket->created_at?->format('Y-m-d H:i') ?? '',
                    $ticket->updated_at?->format('Y-m-d H:i') ?? '',
                ];

                fputcsv($handle, $row, ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
