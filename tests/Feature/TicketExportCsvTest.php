<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketExportCsvTest extends TestCase
{
    use RefreshDatabase;

    public function test_agent_can_download_csv_export(): void
    {
        $agent = User::factory()->state(['role' => 'agent'])->create();
        $owner = User::factory()->state(['role' => 'user'])->create();
        $ticket = Ticket::factory()->for($owner)->create([
            'title' => 'Ticket export',
            'status' => Ticket::STATUS_OPEN,
            'priority' => Ticket::PRIORITY_NORMAL,
        ]);

        $response = $this->actingAs($agent)->get(route('tickets.export.csv'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition');

        $content = $response->streamedContent();
        $this->assertStringStartsWith("\xEF\xBB\xBF", $content);
        $this->assertStringContainsString('id;title;status_label_fr;priority_label_fr;created_by_email;assigned_to_email;created_at;updated_at', $content);
        $this->assertStringContainsString($ticket->title, $content);
    }

    public function test_user_cannot_download_csv_export(): void
    {
        $user = User::factory()->state(['role' => 'user'])->create();

        $response = $this->actingAs($user)->get(route('tickets.export.csv'));

        $response->assertStatus(403);
    }
}
