<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Category::create(['name' => 'Work', 'user_id' => auth()->id() ?? \App\Models\User::first()->id]);
        \App\Models\Category::create(['name' => 'Personal', 'user_id' => auth()->id() ?? \App\Models\User::first()->id]);
    }
}