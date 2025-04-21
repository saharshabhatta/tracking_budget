<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- Add this
use App\Traits\HasStatements;

class UserIncome extends Model
{
    use HasStatements, SoftDeletes;

    protected $fillable = ['user_id', 'monthly_income', 'annual_income', 'month', 'year'];

    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
