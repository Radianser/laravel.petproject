<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Image;

class ImagePolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Image $image): bool
    {
        return $image->user()->is($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Image $image): bool
    {
        return $this->update($user, $image);
    }
}
