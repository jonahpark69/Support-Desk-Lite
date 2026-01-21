<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ToastTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_ticket_sets_success_toast(): void
    {
        Notification::fake();
        $user = User::factory()->state(['role' => 'user'])->create();

        $response = $this->actingAs($user)->post(route('tickets.store'), [
            'title' => 'Demande test',
            'description' => 'Detail du ticket.',
            'priority' => Ticket::PRIORITY_NORMAL,
            'category' => 'Bug',
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('toast.type', 'success');
    }

    public function test_policy_denial_sets_error_toast(): void
    {
        $owner = User::factory()->state(['role' => 'user'])->create();
        $user = User::factory()->state(['role' => 'user'])->create();
        $ticket = Ticket::factory()->for($owner)->create([
            'status' => Ticket::STATUS_OPEN,
        ]);

        $response = $this->actingAs($user)->patch(route('tickets.status', $ticket), [
            'status' => Ticket::STATUS_IN_PROGRESS,
        ]);

        $response->assertStatus(403);
        $response->assertSessionHas('toast.type', 'error');
    }
}
