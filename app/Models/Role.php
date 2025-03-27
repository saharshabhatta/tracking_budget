<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function hasPermission(string $permission, string $role): bool{
        $permission = Permission::whereHas('roles', function ($query) use ($role) {
            $query->where('roles.id', $role);
        })->where('name', $permission)->exists();
        return $permission;
    }
}
