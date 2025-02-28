<?php

namespace App\Policies;

use App\Models\CalendarComment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CalendarCommentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CalendarComment $calendarComment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CalendarComment $calendarComment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CalendarComment $calendarComment): bool
    {
        return $user->id === $calendarComment->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CalendarComment $calendarComment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CalendarComment $calendarComment): bool
    {
        return false;
    }
}
