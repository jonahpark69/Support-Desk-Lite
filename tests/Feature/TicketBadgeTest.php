<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketBadgeTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_shows_open_status_label(): void
    {
        $user = User::factory()->state(['role' => 'user'])->create();
        $ticket = Ticket::factory()->for($user)->create([
            'status' => Ticket::STATUS_OPEN,
        ]);

        $response = $this->actingAs($user)->get(route('tickets.index'));

        $response->assertOk();
        $response->assertSee($ticket->title);
        $response->assertSee('A traiter');
        $response->assertSee('bg-amber-50', false);
    }

    public function test_show_shows_in_progress_status_label(): void
    {
        $user = User::factory()->state(['role' => 'user'])->create();
        $ticket = Ticket::factory()->for($user)->create([
            'status' => Ticket::STATUS_IN_PROGRESS,
        ]);

        $response = $this->actingAs($user)->get(route('tickets.show', $ticket));

        $response->assertOk();
        $response->assertSee($ticket->title);
        $response->assertSee('En cours');
        $response->assertSee('bg-blue-50', false);
    }
}
