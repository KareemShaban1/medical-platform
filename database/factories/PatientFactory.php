<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\Clinic;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    protected $model = Patient::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'clinic_id' => \App\Models\Clinic::factory(),
            'user_id' => \App\Models\User::factory(),
            'phone' => $this->faker->phoneNumber(),
        ];
    }

    /**
     * Create a patient with custom user data
     */
    public function withUserData(array $userData = []): static
    {
        return $this->state(function (array $attributes) use ($userData) {
            return [
                'user_id' => User::factory()->create($userData)->id,
            ];
        });
    }

    /**
     * Create a patient without clinic assignment
     */
    public function withoutClinic(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'clinic_id' => null,
            ];
        });
    }
}
