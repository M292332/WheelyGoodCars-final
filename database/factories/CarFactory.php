<?php

namespace Database\Factories;

use App\Models\Car;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarFactory extends Factory
{
    protected $model = Car::class;

    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'brand' => $this->faker->company,
            'model' => $this->faker->word,
            'seats' => $this->faker->numberBetween(2, 7),
            'doors' => $this->faker->numberBetween(2, 5),
            'weight' => $this->faker->numberBetween(800, 2500),
            'production_year' => $this->faker->numberBetween(1990, date('Y')),
            'color' => $this->faker->safeColorName,
            'mileage' => $this->faker->numberBetween(0, 300000),
            'price' => $this->faker->randomFloat(2, 500, 100000),
            'license_plate' => strtoupper($this->faker->bothify('??-###-??')),
        ];
    }
}