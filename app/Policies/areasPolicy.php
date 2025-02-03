<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Areas;
use Illuminate\Auth\Access\HandlesAuthorization;

class AreasPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_areas');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Areas $areas): bool
    {
        return $user->can('view_areas');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_areas');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Areas $areas): bool
    {
        return $user->can('update_areas');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Areas $areas): bool
    {
        return $user->can('delete_areas');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_areas');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Areas $areas): bool
    {
        return $user->can('force_delete_areas');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_areas');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Areas $areas): bool
    {
        return $user->can('restore_areas');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_areas');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Areas $areas): bool
    {
        return $user->can('replicate_areas');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_areas');
    }
}
