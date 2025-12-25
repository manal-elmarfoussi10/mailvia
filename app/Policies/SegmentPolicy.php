<?php

namespace App\Policies;

use App\Models\Segment;
use App\Models\User;

class SegmentPolicy
{
    public function view(User $user, Segment $segment): bool
    {
        return $user->companies->contains($segment->company_id);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Segment $segment): bool
    {
        return $user->companies->contains($segment->company_id);
    }

    public function delete(User $user, Segment $segment): bool
    {
        return $user->companies->contains($segment->company_id);
    }
}
