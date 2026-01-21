<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketEmptyStateTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_shows_empty_state_when_no_tickets(): void
    {
        $user = User::factory()->state(['role' => 'user'])->create();

        $response = $this->actingAs($user)->get(route('tickets.index'));

        $response->assertOk();
        $response->assertSee('Aucun ticket');
    }

    public function test_index_shows_filtered_empty_state_with_reset(): void
    {
        $user = User::factory()->state(['role' => 'user'])->create();
        Ticket::factory()->for($user)->create([
            'title' => 'Connexion VPN',
        ]);

        $response = $this->actingAs($user)->get(route('tickets.index', [
            'q' => 'inexistant',
        ]));

        $response->assertOk();
        $response->assertSee('Aucun resultat pour ces filtres.');
        $response->assertSee('Reinitialiser');
    }
}
