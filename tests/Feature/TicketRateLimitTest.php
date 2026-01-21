<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TicketRateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_ticket_under_rate_limit(): void
    {
        Notification::fake();
        $user = User::factory()->state(['role' => 'user'])->create();

        $response = $this->actingAs($user)->post(route('tickets.store'), [
            'title' => 'Demande urgente',
            'description' => 'Besoin d assistance.',
            'priority' => Ticket::PRIORITY_NORMAL,
            'category' => 'Support',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseCount('tickets', 1);
    }

    public function test_rate_limit_blocks_excessive_ticket_creation(): void
    {
        Notification::fake();
        $user = User::factory()->state(['role' => 'user'])->create();

        for ($i = 0; $i < 5; $i++) {
            $response = $this->actingAs($user)->post(route('tickets.store'), [
                'title' => 'Demande ' . $i,
                'description' => 'Test limite.',
                'priority' => Ticket::PRIORITY_NORMAL,
                'category' => 'Support',
            ]);

            $response->assertRedirect(route('dashboard'));
        }

        $response = $this->actingAs($user)
            ->from(route('tickets.create'))
            ->post(route('tickets.store'), [
                'title' => 'Demande bloquee',
                'description' => 'Test limite.',
                'priority' => Ticket::PRIORITY_NORMAL,
                'category' => 'Support',
            ]);

        $response->assertRedirect(route('tickets.create'));
        $response->assertSessionHas('toast.type', 'error');
        $this->assertDatabaseCount('tickets', 5);
    }
}
