<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Expense;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category = Category::find(2);
        $expense = Expense::find(1);

        Comment::create([
            'content' => 'This is a sample comment related to category and expense.',
            'user_id' => 2,
            'commentable_type' => get_class($category),
            'commentable_id' => $category->id,
        ]);

        Comment::create([
            'content' => 'This is another comment related to an expense.',
            'user_id' => 2,
            'commentable_type' => get_class($expense),
            'commentable_id' => $expense->id,
        ]);
    }
}
