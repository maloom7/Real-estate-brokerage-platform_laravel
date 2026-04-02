<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PropertyDocument;

class PropertyDocumentPolicy
{
    public function view(User $user, PropertyDocument $document): bool
    {
        if ($user->role === 'admin') return true;
        
        if ($document->is_confidential) {
            return $user->role === 'manager' || 
                   $document->property->agent_id === $user->id;
        }
        
        return $document->property->agent_id === $user->id || 
               $user->role === 'manager';
    }

    public function upload(User $user, PropertyDocument $document): bool
    {
        return in_array($user->role, ['admin', 'manager', 'agent']);
    }

    public function verify(User $user, PropertyDocument $document): bool
    {
        return in_array($user->role, ['admin', 'manager']);
    }

    public function delete(User $user, PropertyDocument $document): bool
    {
        if ($user->role === 'admin') return true;
        return $document->property->agent_id === $user->id;
    }
}