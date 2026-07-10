<?php

namespace App\Policies;

use App\Models\Board;
use App\Models\User;

class BoardPolicy
{
    public function view(User $user, Board $board): bool
    {
        return $board->owner_id === $user->id
            || $board->collaborators()->where('user_id', $user->id)->exists();
    }

    public function update(User $user, Board $board): bool
    {
        return $this->view($user, $board);
    }

    public function delete(User $user, Board $board): bool
    {
        return $board->owner_id === $user->id;
    }

    public function manageCollaborators(User $user, Board $board): bool
    {
        return $board->owner_id === $user->id;
    }
}
