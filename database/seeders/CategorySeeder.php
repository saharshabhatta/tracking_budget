<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        Category::create([
            'name' => 'Groceries',
            'user_id' => 2
        ]);
        Category::create([
            'name' => 'Entertainment',
            'user_id' => 2
        ]);
        Category::create([
            'name' => 'Bills',
            'user_id' => 2
        ]);
        Category::create([
            'name' => 'Transport',
            'user_id' => 2
        ]);
        Category::create([
            'name' => 'Savings',
            'user_id' => 2
        ]);
    }
}
