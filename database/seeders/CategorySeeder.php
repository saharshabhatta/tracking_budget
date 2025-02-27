<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        Category::create(['name' => 'Groceries']);
        Category::create(['name' => 'Entertainment']);
        Category::create(['name' => 'Bills']);
        Category::create(['name' => 'Transport']);
        Category::create(['name' => 'Savings']);
    }
}

