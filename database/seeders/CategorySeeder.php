<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'name' => 'Kopi Panas',
                'description' => 'Kopi panas dengan berbagai varian',
                'image' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Kopi Dingin',
                'description' => 'Kopi dingin dengan berbagai varian',
                'image' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Non-Kopi',
                'description' => 'Minuman non-kopi',
                'image' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Makanan',
                'description' => 'Makanan pendamping kopi',
                'image' => null,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
