<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasStatements;

class Expense extends Model
{
    use HasStatements;

    protected $table = 'expenses';
    protected $fillable = [
        'user_id',
        'category_id',
        'amount',
        'description',
        'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
