<?php

namespace App\Policies;

use App\Models\User;
use App\Models\BiddingType;
use Illuminate\Auth\Access\HandlesAuthorization;

class BiddingTypePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user is 'super_admin' and if he is bypass controls.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }
        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_bidding::type');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BiddingType $biddingType): bool
    {
        return $user->can('view_bidding::type');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_bidding::type');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BiddingType $biddingType): bool
    {
        return $user->can('update_bidding::type');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BiddingType $biddingType): bool
    {
        return $user->can('delete_bidding::type');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_bidding::type');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, BiddingType $biddingType): bool
    {
        return $user->can('force_delete_bidding::type');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_bidding::type');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, BiddingType $biddingType): bool
    {
        return $user->can('restore_bidding::type');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_bidding::type');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, BiddingType $biddingType): bool
    {
        return $user->can('replicate_bidding::type');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_bidding::type');
    }
}
