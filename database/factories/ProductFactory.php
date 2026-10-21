<?php

namespace Database\Factories;

use App\Enums\Availability;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->bothify('Refacción diésel ####');

        return [
            'category_id' => Category::factory(),
            'brand_id' => Brand::factory(),
            'sku' => strtoupper(fake()->bothify('TD-####-??')),
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numerify('####'),
            'description' => fake()->paragraph(),
            'specifications' => [
                'Presión de trabajo' => '1,800 bar',
                'Compatibilidad' => 'Motor 6 cilindros',
                'Garantía' => '6 meses',
            ],
            'price' => fake()->randomFloat(2, 950, 18000),
            'availability' => fake()->randomElement(Availability::cases()),
            'image_path' => null,
        ];
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'availability' => Availability::OutOfStock,
        ]);
    }
}
