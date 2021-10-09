<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'barcode' => $this->faker->numerify('#############'),
            'name' => $this->faker->name(),
            'price' => 1111,
            'cost' => 1001,
            'quantity' => 101,
        ];
    }
}
