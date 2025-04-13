<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'user_categories')->withPivot('spending_percentage')->withTrashed();
    }

    public function userCategories()
    {
        return $this->hasMany(UserCategory::class);
    }

    public function userIncomes()
    {
        return $this->hasMany(UserIncome::class);
    }

    public function statement(){
        return $this->hasMany(Statement::class);
    }

    public function expenses(){
        return $this->hasMany(Expense::class);
    }

    public function roles() {
        return $this->belongsToMany(Role::class);
    }

    protected $casts = [
        'role'=>Role::class,
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }

    public function getRole(){
        return $this->roles()->first();

    }
    public function hasRole(string $roleName): bool {
        return $this->roles()->where('name', $roleName)->exists();
    }

    public function hasPermission(string $permission, string $role): bool{
        $permission = Permission::whereHas('roles', function ($query) use ($role) {
            $query->where('id', $role);
        })->where('name', $permission)->exists();
        return $permission;
    }
}
