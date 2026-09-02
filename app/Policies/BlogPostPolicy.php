<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\BlogPost;
use App\Models\User;

/**
 * BlogPostPolicy — controls blog content management.
 * Blog is a platform-level feature, not tenant-specific.
 * Only platform admins (Gate::before) can manage blog.
 * All other users can only read published posts publicly (no auth required).
 */
final class BlogPostPolicy
{
    public function viewAny(User $user): bool
    {
        return false; // Platform admin only via Gate::before
    }

    public function view(User $user, BlogPost $post): bool
    {
        return false; // Platform admin only
    }

    public function create(User $user): bool
    {
        return false; // Platform admin only
    }

    public function update(User $user, BlogPost $post): bool
    {
        return false; // Platform admin only
    }

    public function delete(User $user, BlogPost $post): bool
    {
        return false; // Platform admin only
    }

    public function publish(User $user, BlogPost $post): bool
    {
        return false; // Platform admin only
    }

    public function restore(User $user, BlogPost $post): bool
    {
        return false; // Platform admin only
    }
}
