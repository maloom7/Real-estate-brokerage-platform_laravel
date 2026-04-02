<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'role', 'status',
        'permissions', 'manager_id', 'profile_photo_path'
    ];

    protected $hidden = [
        'password', 'remember_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'permissions' => 'array'
    ];

    public function properties()
    {
        return $this->hasMany(Property::class, 'agent_id');
    }

    public function managedUsers()
    {
        return $this->hasMany(User::class, 'manager_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function deals()
    {
        return $this->hasMany(Deal::class, 'agent_id');
    }

    public function activityLogs()
    {
        return $this->hasMany(\Spatie\Activitylog\Models\Activity::class, 'causer_id');
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isManager()
    {
        return $this->role === 'manager';
    }

    public function isAgent()
    {
        return $this->role === 'agent';
    }

    public function canAccessProperty(Property $property)
    {
        if ($this->isAdmin()) return true;
        if ($this->id === $property->agent_id) return true;
        if ($this->isManager() && $this->id === $property->agent->manager_id) return true;
        return $property->visibility !== 'internal';
    }

    public function hasPermission(string $permission)
    {
        if ($this->isAdmin()) return true;
        return in_array($permission, $this->permissions ?? []);
    }
}