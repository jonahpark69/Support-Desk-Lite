<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAgentsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_agents_page(): void
    {
        $admin = User::factory()->state(['role' => 'admin'])->create();
        $agent = User::factory()->state(['role' => 'agent'])->create([
            'name' => 'Agent Test',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.agents.index'));

        $response->assertOk();
        $response->assertSee('Agents');
        $response->assertSee($agent->name);
    }

    public function test_non_admin_is_forbidden_from_agents_page(): void
    {
        $user = User::factory()->state(['role' => 'user'])->create();

        $response = $this->actingAs($user)->get(route('admin.agents.index'));

        $response->assertStatus(403);
    }
}
