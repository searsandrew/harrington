<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function create(User $user): bool
    {
        return true; // Any authenticated user can create
    }

    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id || (bool) ($user->is_admin ?? false);
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id || (bool) ($user->is_admin ?? false);
    }
}
