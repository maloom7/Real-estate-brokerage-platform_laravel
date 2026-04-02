<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Property;

class PropertyPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Property $property): bool
    {
        if ($user->role === 'admin') return true;
        if ($user->role === 'manager') return true;
        return $property->agent_id === $user->id || $property->visibility === 'public';
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'manager', 'agent']);
    }

    public function update(User $user, Property $property): bool
    {
        if ($user->role === 'admin') return true;
        if ($user->role === 'manager') return true;
        return $property->agent_id === $user->id;
    }

    public function delete(User $user, Property $property): bool
    {
        return $user->role === 'admin' || $property->agent_id === $user->id;
    }

    public function restore(User $user, Property $property): bool
    {
        return $user->role === 'admin';
    }

    public function forceDelete(User $user, Property $property): bool
    {
        return $user->role === 'admin';
    }
}