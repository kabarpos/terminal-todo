<?php

namespace App\Policies;

use App\Models\TaskComment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskCommentPolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
        return $user->hasPermissionTo('manage-task') || 
            $user->hasRole('Super Admin') ||
            $user->hasRole('Content Manager');
    }

    public function update(User $user, TaskComment $comment)
    {
        return $user->id === $comment->user_id || 
            $user->hasRole('Super Admin') ||
            $user->hasRole('Content Manager');
    }

    public function delete(User $user, TaskComment $comment)
    {
        return $user->id === $comment->user_id || 
               $user->hasRole('Super Admin') || 
               $user->hasRole('Content Manager');
    }
} 