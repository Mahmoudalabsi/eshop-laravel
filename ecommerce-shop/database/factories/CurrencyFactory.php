<?php

namespace Database\Factories;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

class CurrencyFactory extends Factory
{
    protected $model = Currency::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'code' => strtoupper($this->faker->unique()->lexify('???')),
            'symbol' => $this->faker->randomElement(['$', '€', '£', 'د.إ', 'ر.س']),
            'exchange_rate' => $this->faker->randomFloat(4, 0.1, 10),
            'is_default' => false,
            'status' => true,
        ];
    }

    public function default(): self
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
            'exchange_rate' => 1.0,
        ]);
    }
}
