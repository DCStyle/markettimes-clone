<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'editor', 'author']);
    }

    public function view(User $user, Article $article): bool
    {
        if (in_array($user->role, ['admin', 'editor'])) {
            return true;
        }

        return $user->role === 'author' && $article->author_id === $user->id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'editor', 'author']);
    }

    public function update(User $user, Article $article): bool
    {
        if (in_array($user->role, ['admin', 'editor'])) {
            return true;
        }

        return $user->role === 'author' && $article->author_id === $user->id;
    }

    public function delete(User $user, Article $article): bool
    {
        if (in_array($user->role, ['admin', 'editor'])) {
            return true;
        }

        return $user->role === 'author' && $article->author_id === $user->id;
    }

    public function deleteAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'editor', 'author']);
    }

    public function forceDelete(User $user, Article $article): bool
    {
        return in_array($user->role, ['admin', 'editor']);
    }

    public function forceDeleteAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'editor']);
    }

    public function restore(User $user, Article $article): bool
    {
        return in_array($user->role, ['admin', 'editor']);
    }

    public function restoreAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'editor']);
    }

    public function reorder(User $user): bool
    {
        return in_array($user->role, ['admin', 'editor']);
    }
}
