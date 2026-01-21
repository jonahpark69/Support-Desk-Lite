<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use App\Notifications\NewCommentNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationPreferencesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_disable_new_comment_notifications(): void
    {
        $user = User::factory()->state(['role' => 'user'])->create([
            'notify_new_comment' => true,
        ]);

        $response = $this->actingAs($user)->patch(route('profile.notifications.update'), [
            'notify_new_comment' => false,
        ]);

        $response->assertRedirect(route('profile.edit'));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'notify_new_comment' => false,
        ]);
    }

    public function test_user_does_not_receive_new_comment_notification_when_disabled(): void
    {
        Notification::fake();

        $owner = User::factory()->state(['role' => 'user'])->create([
            'notify_new_comment' => false,
        ]);
        $assignee = User::factory()->state(['role' => 'agent'])->create([
            'notify_new_comment' => true,
        ]);
        $commenter = User::factory()->state(['role' => 'agent'])->create([
            'notify_new_comment' => true,
        ]);

        $ticket = Ticket::factory()->for($owner)->create([
            'assigned_to' => $assignee->id,
        ]);

        $response = $this->actingAs($commenter)->post(route('tickets.comments.store', $ticket), [
            'body' => 'Commentaire test.',
        ]);

        $response->assertRedirect();
        Notification::assertNotSentTo($owner, NewCommentNotification::class);
        Notification::assertSentToTimes($assignee, NewCommentNotification::class, 1);
    }
}
