<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = ['name'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role')->withTimestamps();
    }

    public function hasPermission(string $permission, int $roleId): bool
    {
        return Permission::whereHas('roles', function ($query) use ($roleId) {
            $query->where('roles.id', $roleId);
        })->where('name', $permission)->exists();
    }
}
