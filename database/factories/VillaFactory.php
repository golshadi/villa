<?php

namespace Database\Factories;

use App\Models\Villa;
use Illuminate\Database\Eloquent\Factories\Factory;

class VillaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Villa::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->title,
            'type' => 'ویلایی',
            'phone_number' => $this->faker->phoneNumber,
            'story' => $this->faker->text,
            'state' => $this->faker->state,
            'city' => $this->faker->city,
            'village' => $this->faker->streetName,
            'postal_code' => $this->faker->languageCode,
            'address' => $this->faker->address,
            'long' => $this->faker->randomNumber(),
            'lat' => $this->faker->randomNumber(),
            'user_id' => 1,
            'status'=>0

        ];
    }
}
