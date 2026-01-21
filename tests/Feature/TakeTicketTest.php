<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TakeTicketTest extends TestCase
{
    use RefreshDatabase;

    public function test_agent_can_take_unassigned_ticket(): void
    {
        $agent = User::factory()->state(['role' => 'agent'])->create();
        $owner = User::factory()->state(['role' => 'user'])->create();
        $ticket = Ticket::factory()->for($owner)->create([
            'assigned_to' => null,
            'status' => Ticket::STATUS_OPEN,
        ]);

        $response = $this->actingAs($agent)->post(route('tickets.take', $ticket));

        $response->assertRedirect(route('tickets.show', $ticket));
        $response->assertSessionHas('toast.type', 'success');
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'assigned_to' => $agent->id,
            'status' => Ticket::STATUS_IN_PROGRESS,
        ]);
    }

    public function test_agent_cannot_take_ticket_assigned_to_another_agent(): void
    {
        $agent = User::factory()->state(['role' => 'agent'])->create();
        $otherAgent = User::factory()->state(['role' => 'agent'])->create();
        $ticket = Ticket::factory()->create([
            'assigned_to' => $otherAgent->id,
            'status' => Ticket::STATUS_OPEN,
        ]);

        $response = $this->actingAs($agent)->post(route('tickets.take', $ticket));

        $response->assertStatus(403);
        $response->assertSessionHas('toast.type', 'error');
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'assigned_to' => $otherAgent->id,
            'status' => Ticket::STATUS_OPEN,
        ]);
    }
}
