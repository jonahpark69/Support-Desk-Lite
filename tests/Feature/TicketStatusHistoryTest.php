<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketStatusHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_change_creates_history_entry(): void
    {
        $agent = User::factory()->state(['role' => 'agent'])->create();
        $ticket = Ticket::factory()->create([
            'status' => Ticket::STATUS_OPEN,
        ]);

        $response = $this->actingAs($agent)
            ->from(route('tickets.show', $ticket))
            ->patch(route('tickets.status', $ticket), [
                'status' => Ticket::STATUS_IN_PROGRESS,
            ]);

        $response->assertRedirect(route('tickets.show', $ticket));
        $this->assertDatabaseHas('ticket_status_changes', [
            'ticket_id' => $ticket->id,
            'from_status' => Ticket::STATUS_OPEN,
            'to_status' => Ticket::STATUS_IN_PROGRESS,
            'changed_by' => $agent->id,
        ]);
    }

    public function test_same_status_does_not_create_history_entry(): void
    {
        $agent = User::factory()->state(['role' => 'agent'])->create();
        $ticket = Ticket::factory()->create([
            'status' => Ticket::STATUS_OPEN,
        ]);

        $response = $this->actingAs($agent)
            ->from(route('tickets.show', $ticket))
            ->patch(route('tickets.status', $ticket), [
                'status' => Ticket::STATUS_OPEN,
            ]);

        $response->assertRedirect(route('tickets.show', $ticket));
        $this->assertDatabaseCount('ticket_status_changes', 0);
    }
}
