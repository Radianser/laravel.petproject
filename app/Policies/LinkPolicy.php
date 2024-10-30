<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Link;

class LinkPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Link $link): bool
    {
        return $link->user()->is($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Link $link): bool
    {
        return $this->update($user, $link);
    }
}
