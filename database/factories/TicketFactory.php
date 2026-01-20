<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(6),
            'description' => fake()->boolean(70) ? fake()->paragraph(3) : null,
            'status' => fake()->randomElement([
                Ticket::STATUS_OPEN,
                Ticket::STATUS_IN_PROGRESS,
                Ticket::STATUS_RESOLVED,
                Ticket::STATUS_CLOSED,
            ]),
            'priority' => fake()->randomElement([
                Ticket::PRIORITY_LOW,
                Ticket::PRIORITY_NORMAL,
                Ticket::PRIORITY_HIGH,
                Ticket::PRIORITY_URGENT,
            ]),
            'category' => fake()->boolean(60) ? fake()->randomElement(['Bug', 'Question', 'Billing']) : null,
            'assigned_to' => fake()->boolean(40)
                ? User::factory()->state(['role' => 'agent'])
                : null,
            'resolved_at' => null,
        ];
    }
}
