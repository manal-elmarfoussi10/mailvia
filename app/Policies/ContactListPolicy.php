<?php

namespace App\Policies;

use App\Models\ContactList;
use App\Models\User;

class ContactListPolicy
{
    public function view(User $user, ContactList $list): bool
    {
        return $user->companies->contains($list->company_id);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ContactList $list): bool
    {
        return $user->companies->contains($list->company_id);
    }

    public function delete(User $user, ContactList $list): bool
    {
        return $user->companies->contains($list->company_id);
    }
}
