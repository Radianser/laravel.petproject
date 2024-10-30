<?php

namespace App\Policies;

use App\Models\User;
use App\Models\File;

class FilePolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, File $file): bool
    {
        return $file->user()->is($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, File $file): bool
    {
        return $this->update($user, $file);
    }
}
