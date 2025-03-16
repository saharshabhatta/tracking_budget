<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'user_id'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_categories')->withPivot('spending_percentage');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }


}

