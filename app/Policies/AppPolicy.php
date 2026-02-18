<?php

namespace App\Policies;

use App\Models\App;
use App\Models\User;

class AppPolicy
{
    /**
     * User can only view/edit/delete apps they own.
     */
    public function viewAny(User $user): bool
    {
        return $user->canAccessApp();
    }

    public function view(User $user, App $app): bool
    {
        return $user->canAccessApp() && (int) $app->user_id === (int) $user->id;
    }

    public function create(User $user): bool
    {
        return $user->canAccessApp();
    }

    public function update(User $user, App $app): bool
    {
        return (int) $app->user_id === (int) $user->id;
    }

    public function delete(User $user, App $app): bool
    {
        return (int) $app->user_id === (int) $user->id;
    }

    public function storeApiKey(User $user, App $app): bool
    {
        return (int) $app->user_id === (int) $user->id;
    }

    public function revokeApiKey(User $user, App $app): bool
    {
        return (int) $app->user_id === (int) $user->id;
    }
}
