<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasStatements;

class UserIncome extends Model
{
    use HasStatements;

    protected $fillable = ['user_id', 'monthly_income', 'annual_income', 'month', 'year'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
