<?php

namespace Database\Factories;

use App\Enums\StoreStateEnum;
use App\Enums\StoreTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Store>
 */
class StoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'lat' => $this->faker->latitude,
            'long' => $this->faker->longitude,
            'state' => $this->faker->randomElement(StoreStateEnum::cases()),
            'type' => $this->faker->randomElement(StoreTypeEnum::cases()),
            'max_delivery_distance' => $this->faker->randomNumber(2),
        ];
    }
}
