<?php

namespace Database\Factories;

use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;

class BrandFactory extends Factory
{
    protected $model = Brand::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'slug' => $this->faker->slug(),
            'code' => strtoupper($this->faker->lexify('???')),
            'description' => $this->faker->paragraph(2),
            'logo_path' => 'brands/' . $this->faker->image('public/uploads/brands', 200, 200, 'business', false),
            'website' => $this->faker->optional(0.7)->url(),
            'is_active' => $this->faker->boolean(90),
            'display_order' => $this->faker->numberBetween(0, 100),
            'meta_title' => $this->faker->sentence(4),
            'meta_description' => $this->faker->sentence(10),
            'meta' => [
                'founded_year' => $this->faker->numberBetween(1900, 2020),
                'country' => $this->faker->country(),
                'industry' => $this->faker->randomElement(['Electronics', 'Technology', 'Consumer Goods']),
            ],
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
