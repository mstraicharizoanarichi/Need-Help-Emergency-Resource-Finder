<?php

namespace Database\Factories;

use App\Models\Resource;
use App\Models\ResourceCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Resource>
 */
class ResourceFactory extends Factory
{
    protected $model = Resource::class;

    public function definition(): array
    {
        $name = fake()->company() . ' Hospital';

        return [
            'user_id' => User::factory(),
            'category_id' => ResourceCategory::first()?->id ?? 1,
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(6),
            'description' => fake()->paragraph(),
            'address' => fake()->address(),
            'area' => 'Dhanmondi',
            'city' => 'Dhaka',
            'district' => 'Dhaka',
            'postal_code' => '1205',
            'latitude' => 23.7465,
            'longitude' => 90.3760,
            'phone' => '+880171' . fake()->numerify('#######'),
            'emergency_phone' => '+880181' . fake()->numerify('#######'),
            'verification_status' => 'verified',
            'availability_status' => 'available',
            'is_24_7' => true,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_status' => 'pending',
            'verified_at' => null,
            'verified_by' => null,
        ]);
    }
}
