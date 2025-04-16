<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Category;
use App\Models\UserCategory;
use Illuminate\Support\Facades\Auth;

class UniqueUserCategory implements Rule
{
    public function passes($attribute, $value): bool
    {
        $userId = Auth::id();
        $categoryName = strtolower(trim($value));

        $category = Category::withTrashed()
            ->whereRaw('LOWER(name) = ?', [$categoryName])
            ->first();

        if (!$category) {
            return true;
        }

        if ($category->trashed() && $category->user_id == $userId) {
            return true;
        }

        $alreadyLinked = UserCategory::where('user_id', $userId)
            ->where('category_id', $category->id)
            ->exists();

        return !$alreadyLinked;
    }

    public function message(): string
    {
        return 'This category already exists. Please choose a different name.';
    }
}
