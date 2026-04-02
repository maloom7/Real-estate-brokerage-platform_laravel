<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Client;

class ClientPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Client $client): bool
    {
        if ($user->role === 'admin') return true;
        if ($user->role === 'manager') return true;
        return $client->assigned_agent === $user->id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'manager', 'agent']);
    }

    public function update(User $user, Client $client): bool
    {
        if ($user->role === 'admin') return true;
        if ($user->role === 'manager') return true;
        return $client->assigned_agent === $user->id;
    }

    public function delete(User $user, Client $client): bool
    {
        return $user->role === 'admin';
    }
}