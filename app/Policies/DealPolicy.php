<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Deal;

class DealPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Deal $deal): bool
    {
        if ($user->role === 'admin') return true;
        if ($user->role === 'manager') return true;
        return $deal->agent_id === $user->id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'manager', 'agent']);
    }

    public function update(User $user, Deal $deal): bool
    {
        if ($user->role === 'admin') return true;
        if ($user->role === 'manager') return true;
        return $deal->agent_id === $user->id;
    }

    public function delete(User $user, Deal $deal): bool
    {
        return $user->role === 'admin';
    }
}