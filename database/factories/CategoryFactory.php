<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition()
    {
        $name = $this->faker->unique()->word();
        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence(10),
            'icon' => $this->faker->randomElement(['fa-calendar', 'fa-music', 'fa-users', 'fa-briefcase', 'fa-graduation-cap']),
            'color' => $this->faker->safeHexColor(),
            'image' => null,
            'created_by' => \App\Models\User::factory(),
        ];
    }
} 