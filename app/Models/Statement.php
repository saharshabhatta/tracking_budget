<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Statement extends Model
{
    public function statementable() : MorphTo
    {
        return $this->morphTo();
    }
}
