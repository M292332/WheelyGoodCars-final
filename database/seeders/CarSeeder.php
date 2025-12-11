<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Car;
use App\Models\Tag;
use App\Models\User;

class CarSeeder extends Seeder
{
    public function run(): void
    {
        $tags = Tag::all()->pluck('id')->toArray();
        $images = [
            'car_photos/car1.jpg',
            'car_photos/car2.jpg',
            'car_photos/car3.jpg',
            'car_photos/car4.jpg',
            'car_photos/car5.jpg',
        ];

        Car::factory(250)->create()->each(function ($car) use ($tags, $images) {
            $car->license_plate = str_replace('-', '', $car->license_plate);

            $car->brand = strtoupper($car->brand);
            $car->model = strtoupper($car->model);
            $car->color = strtoupper($car->color);

            // Zet de created_at op een willekeurige datum binnen het laatste kwartaal
            $car->created_at = now()->subDays(rand(0, 90));
            $car->updated_at = $car->created_at;

            $rand = rand(1, 100);
            if ($rand <= 40) {
                $car->views = 0;
            } elseif ($rand <= 80) {
                $car->views = rand(1, 5);
            } elseif ($rand <= 95) {
                $car->views = rand(6, 99);
            } else {
                $car->views = rand(100, 250);
            }

            $randomTags = collect($tags)->shuffle()->take(rand(0, 3))->toArray();
            $car->tags()->sync($randomTags);

            if (rand(1, 20) === 1) {
                $car->image = null;
            } else {
                $car->image = $images[array_rand($images)];
            }
            $car->save();
        });

        $soldCount = rand(50, 100);
        Car::factory($soldCount)->create([
            'status' => 'sold',
            'sold_at' => now()->subDays(rand(1, 365)),
        ])->each(function ($car) use ($tags, $images) {
            $car->license_plate = str_replace('-', '', $car->license_plate);

            $car->brand = strtoupper($car->brand);
            $car->model = strtoupper($car->model);
            $car->color = strtoupper($car->color);

            // Zet de created_at op een willekeurige datum binnen het laatste kwartaal
            $car->created_at = now()->subDays(rand(0, 90));
            $car->updated_at = $car->created_at;

            $rand = rand(1, 100);
            if ($rand <= 40) {
                $car->views = 0;
            } elseif ($rand <= 80) {
                $car->views = rand(1, 5);
            } elseif ($rand <= 95) {
                $car->views = rand(6, 99);
            } else {
                $car->views = rand(100, 250);
            }

            $randomTags = collect($tags)->shuffle()->take(rand(0, 3))->toArray();
            $car->tags()->sync($randomTags);

            if (rand(1, 20) === 1) {
                $car->image = null;
            } else {
                $car->image = $images[array_rand($images)];
            }
            $car->save();
        });

        $testUsers = [
            ['email' => 'testuser1@example.com'],
            ['email' => 'testuser2@example.com'],
        ];

        foreach ($testUsers as $userData) {
            $user = User::where('email', $userData['email'])->first();
            if ($user) {
                $car = Car::factory()->create([
                    'user_id' => $user->id,
                    'image' => $images[array_rand($images)],
                ]);
                $randomTags = collect($tags)->shuffle()->take(rand(0, 3))->toArray();
                $car->tags()->sync($randomTags);
            }
        }
    }
}