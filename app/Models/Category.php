<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'user_id'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_categories')->withPivot('spending_percentage');
    }
    public function expenses(){
        return $this->hasMany(Expense::class);
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}

