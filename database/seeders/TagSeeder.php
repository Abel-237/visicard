<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run()
    {
        \App\Models\Tag::factory(15)->create();
    }
} 