<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        // TODO: Implement view any policy
        return true;
    }

    public function view(User $user, $model): bool
    {
        // TODO: Implement view policy
        return true;
    }

    public function create(User $user): bool
    {
        // TODO: Implement create policy
        return true;
    }

    public function update(User $user, $model): bool
    {
        // TODO: Implement update policy
        return true;
    }

    public function delete(User $user, $model): bool
    {
        // TODO: Implement delete policy
        return true;
    }
}
