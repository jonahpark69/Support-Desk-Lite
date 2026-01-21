<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use App\Notifications\NewCommentNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NewCommentNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_agent_comment_notifies_ticket_owner_only(): void
    {
        Notification::fake();

        $owner = User::factory()->state(['role' => 'user'])->create();
        $agent = User::factory()->state(['role' => 'agent'])->create();
        $ticket = Ticket::factory()->for($owner)->create([
            'assigned_to' => $agent->id,
        ]);

        $response = $this->actingAs($agent)->post(route('tickets.comments.store', $ticket), [
            'body' => 'Prise en charge du ticket.',
        ]);

        $response->assertRedirect();
        Notification::assertSentToTimes($owner, NewCommentNotification::class, 1);
        Notification::assertNotSentTo($agent, NewCommentNotification::class);
    }

    public function test_user_comment_notifies_assigned_agent_only(): void
    {
        Notification::fake();

        $owner = User::factory()->state(['role' => 'user'])->create();
        $agent = User::factory()->state(['role' => 'agent'])->create();
        $ticket = Ticket::factory()->for($owner)->create([
            'assigned_to' => $agent->id,
        ]);

        $response = $this->actingAs($owner)->post(route('tickets.comments.store', $ticket), [
            'body' => 'Merci pour votre retour.',
        ]);

        $response->assertRedirect();
        Notification::assertSentToTimes($agent, NewCommentNotification::class, 1);
        Notification::assertNotSentTo($owner, NewCommentNotification::class);
    }
}
