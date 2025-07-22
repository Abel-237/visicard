<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run()
    {
        \App\Models\Event::factory(30)->create()->each(function ($event) {
            $tags = \App\Models\Tag::inRandomOrder()->take(rand(1, 4))->pluck('id');
            $event->tags()->sync($tags);
        });
    }
} 