<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_view_other_users_ticket(): void
    {
        $owner = User::factory()->state(['role' => 'user'])->create();
        $otherUser = User::factory()->state(['role' => 'user'])->create();
        $ticket = Ticket::factory()->for($owner)->create([
            'title' => 'Ticket prive',
        ]);

        $response = $this->actingAs($otherUser)->get(route('tickets.show', $ticket));

        $this->assertTrue(in_array($response->status(), [403, 404], true));
        $response->assertDontSee('Ticket prive');
    }

    public function test_only_agent_can_change_ticket_status(): void
    {
        $owner = User::factory()->state(['role' => 'user'])->create();
        $agent = User::factory()->state(['role' => 'agent'])->create();
        $ticket = Ticket::factory()->for($owner)->create([
            'status' => Ticket::STATUS_OPEN,
        ]);

        $response = $this->actingAs($owner)
            ->from(route('tickets.show', $ticket))
            ->patch(route('tickets.status', $ticket), [
                'status' => Ticket::STATUS_IN_PROGRESS,
            ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => Ticket::STATUS_OPEN,
        ]);

        $response = $this->actingAs($agent)
            ->from(route('tickets.show', $ticket))
            ->patch(route('tickets.status', $ticket), [
                'status' => Ticket::STATUS_IN_PROGRESS,
            ]);

        $response->assertRedirect(route('tickets.show', $ticket));
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => Ticket::STATUS_IN_PROGRESS,
        ]);
    }
}
