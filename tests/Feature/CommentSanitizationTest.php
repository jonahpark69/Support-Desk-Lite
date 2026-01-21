<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentSanitizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_comment_with_script_tag_is_sanitized(): void
    {
        $user = User::factory()->state(['role' => 'user'])->create();
        $ticket = Ticket::factory()->for($user)->create();

        $response = $this->actingAs($user)->post(route('tickets.comments.store', $ticket), [
            'body' => '<script>alert(1)</script>',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'body' => 'alert(1)',
        ]);

        $response = $this->actingAs($user)->get(route('tickets.show', $ticket));
        $response->assertOk();
        $response->assertSee('alert(1)', false);
        $response->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_valid_comment_is_stored_and_visible(): void
    {
        $user = User::factory()->state(['role' => 'user'])->create();
        $ticket = Ticket::factory()->for($user)->create();

        $response = $this->actingAs($user)->post(route('tickets.comments.store', $ticket), [
            'body' => "Bonjour\nSupport",
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'body' => "Bonjour\nSupport",
        ]);

        $response = $this->actingAs($user)->get(route('tickets.show', $ticket));
        $response->assertOk();
        $response->assertSee('Bonjour', false);
        $response->assertSee('Support', false);
    }
}
