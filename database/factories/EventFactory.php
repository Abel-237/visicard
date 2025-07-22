<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition()
    {
        $title = $this->faker->sentence(6);
        $status = $this->faker->randomElement(['draft', 'published']);
        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . $this->faker->unique()->numberBetween(1, 99999),
            'content' => $this->faker->paragraphs(3, true),
            'excerpt' => $this->faker->sentence(12),
            'category_id' => Category::factory(),
            'user_id' => User::factory(),
            'event_date' => $this->faker->dateTimeBetween('+1 days', '+1 year'),
            'location' => $this->faker->city(),
            'status' => $status,
            'published_at' => $status === 'published' ? $this->faker->dateTimeBetween('-1 year', 'now') : null,
            'featured' => $this->faker->boolean(20),
            'image' => null,
        ];
    }
} 