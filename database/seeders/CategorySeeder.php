<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Laptop',     'slug' => 'laptop',     'description' => 'Laptop dan notebook berbagai merek'],
            ['name' => 'Aksesoris',  'slug' => 'aksesoris',  'description' => 'Aksesoris komputer dan laptop'],
            ['name' => 'Komponen',   'slug' => 'komponen',   'description' => 'Komponen dan spare part komputer'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
