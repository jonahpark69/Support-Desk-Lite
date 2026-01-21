<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use App\Services\AssignmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignmentServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_assigns_ticket_to_agent_with_fewest_active_tickets(): void
    {
        $agentA = User::factory()->state(['role' => 'agent'])->create();
        $agentB = User::factory()->state(['role' => 'agent'])->create();

        Ticket::factory()->count(2)->create([
            'assigned_to' => $agentA->id,
            'status' => Ticket::STATUS_OPEN,
        ]);

        Ticket::factory()->create([
            'assigned_to' => $agentB->id,
            'status' => Ticket::STATUS_IN_PROGRESS,
        ]);

        Ticket::factory()->create([
            'assigned_to' => $agentB->id,
            'status' => Ticket::STATUS_RESOLVED,
        ]);

        $ticket = Ticket::factory()->create([
            'assigned_to' => null,
            'status' => Ticket::STATUS_OPEN,
        ]);

        $service = app(AssignmentService::class);
        $assignedAgent = $service->assignTicketIfNeeded($ticket);

        $this->assertNotNull($assignedAgent);
        $this->assertSame($agentB->id, $assignedAgent->id);
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'assigned_to' => $agentB->id,
        ]);
    }

    public function test_does_not_reassign_ticket_when_already_assigned(): void
    {
        $agent = User::factory()->state(['role' => 'agent'])->create();
        $otherAgent = User::factory()->state(['role' => 'agent'])->create();

        $ticket = Ticket::factory()->create([
            'assigned_to' => $agent->id,
            'status' => Ticket::STATUS_OPEN,
        ]);

        $service = app(AssignmentService::class);
        $assignedAgent = $service->assignTicketIfNeeded($ticket);

        $this->assertNull($assignedAgent);
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'assigned_to' => $agent->id,
        ]);
        $this->assertDatabaseMissing('tickets', [
            'id' => $ticket->id,
            'assigned_to' => $otherAgent->id,
        ]);
    }
}
