<?php
namespace Database\Factories;

use App\Models\Checkout;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CheckoutItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'checkout_id' => Checkout::factory(),
            'product_id' => Product::factory(),
            'provider_id' => User::factory(),
            'category_id' => Category::factory(),
            'quantity' => $this->faker->numberBetween(1, 5),
            'price' => $this->faker->randomFloat(2, 10, 500),
            'final_price' => function (array $attributes) {
                return $attributes['quantity'] * $attributes['price'];
            },
        ];
    }
}
