<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Machine categories used across the arcade floor.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Racing', 'description' => 'Mesin dengan tema game balapan.'],
            ['name' => 'Fighting', 'description' => 'Game pertarungan 1v1 ataupun vs bot.'],
            ['name' => 'Shooting', 'description' => 'Game dengan esensi tembak menembak.'],
            ['name' => 'Claw Machine', 'description' => 'Mesin capit untuk boneka dan hadiah.'],
            ['name' => 'Ticket', 'description' => 'Minigames dengan tujuan utamanya adalah tiket.'],
            ['name' => 'Sport', 'description' => 'Permainan yang memiliki element olahraga basket, bola, dll.'],
            ['name' => 'Rhythm', 'description' => 'Game musik.'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
