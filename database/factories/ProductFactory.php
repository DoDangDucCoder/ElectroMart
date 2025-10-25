<?php

namespace Database\Factories;

use App\Infrastructure\Persistence\Eloquent\Catalog\Product;
use App\Infrastructure\Persistence\Eloquent\Catalog\Brand;
use App\Infrastructure\Persistence\Eloquent\Catalog\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'slug' => $this->faker->slug(),
            'sku' => strtoupper($this->faker->bothify('SKU-###-???')),
            'barcode' => $this->faker->ean13(),
            'unit' => 'Chiếc',
            'price' => $this->faker->numberBetween(1000000, 50000000),
            'sale_price' => $this->faker->optional(0.3)->numberBetween(500000, 45000000),
            'cost_price' => $this->faker->numberBetween(500000, 40000000),
            'stock_quantity' => $this->faker->numberBetween(0, 1000),
            'stock_alert_threshold' => $this->faker->numberBetween(5, 50),
            'is_active' => $this->faker->boolean(90),
            'is_featured' => $this->faker->boolean(20),
            'display_order' => $this->faker->numberBetween(0, 100),
            'thumbnail_path' => 'products/' . $this->faker->image('public/uploads/products', 400, 400, 'electronics', false),
            'description' => $this->faker->paragraph(3),
            'content' => $this->faker->paragraphs(5, true),
            'views_count' => $this->faker->numberBetween(0, 10000),
            'meta' => [
                'tags' => $this->faker->words(5),
                'specifications' => [
                    'weight' => $this->faker->numberBetween(100, 2000) . 'g',
                    'dimensions' => $this->faker->numberBetween(100, 300) . 'x' . $this->faker->numberBetween(50, 200) . 'x' . $this->faker->numberBetween(5, 50) . 'mm',
                    'color' => $this->faker->colorName(),
                ]
            ],
            'meta_title' => $this->faker->sentence(6),
            'meta_description' => $this->faker->sentence(15),
            'brand_id' => Brand::factory(),
            'primary_category_id' => ProductCategory::factory(),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'is_active' => true,
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_quantity' => 0,
        ]);
    }

    public function lowStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_quantity' => $this->faker->numberBetween(1, 5),
            'stock_alert_threshold' => 10,
        ]);
    }
}
