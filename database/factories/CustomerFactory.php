<?php

namespace Database\Factories;

use App\Infrastructure\Persistence\Eloquent\Customer\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail(),
            'name' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'password' => Hash::make('password'),
            'is_active' => $this->faker->boolean(90),
            'email_verified_at' => $this->faker->optional(0.8)->dateTimeBetween('-1 year', 'now'),
            'phone_verified_at' => $this->faker->optional(0.6)->dateTimeBetween('-1 year', 'now'),
            'loyalty_points_balance' => $this->faker->numberBetween(0, 10000),
            'meta' => [
                'preferred_language' => $this->faker->randomElement(['vi', 'en']),
                'marketing_consent' => $this->faker->boolean(70),
                'birth_date' => $this->faker->optional(0.7)->date('Y-m-d', '2000-01-01'),
                'gender' => $this->faker->optional(0.6)->randomElement(['male', 'female']),
            ],
        ];
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
            'phone_verified_at' => null,
        ]);
    }

    public function vip(): static
    {
        return $this->state(fn (array $attributes) => [
            'loyalty_points_balance' => $this->faker->numberBetween(5000, 50000),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}
