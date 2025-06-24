<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostPolicy
{
  public function create(User $user) { return $user->type === 'admin'; }
    public function update(User $user, Post $post) { return $user->type === 'admin'; }
    public function delete(User $user, Post $post) { return $user->type === 'admin'; }
    public function toggleVisibility(User $user, Post $post) { return $user->type === 'admin'; }
}
