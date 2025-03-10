<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\Statement;

trait HasStatements
{
    public function statements(): MorphMany
    {
        return $this->morphMany(Statement::class, 'statementable');
    }
}
