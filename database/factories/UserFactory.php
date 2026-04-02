<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'numero_documento' => (string) fake()->unique()->numberBetween(100000, 999999999),
            'user_nombre' => fake()->name(),
            'user_correo' => fake()->unique()->safeEmail(),
            'google_id' => null,
            'avatar' => null,
            'user_telefono' => '3' . fake()->numerify('#########'),
            'user_contrasena' => 'password',
            'email_verified_at' => now(),
            'rol_id' => 3,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
