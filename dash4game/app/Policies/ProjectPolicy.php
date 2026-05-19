<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function view(User $user, Project $project): bool
    {
        return $project->hasMember($user);
    }

    public function update(User $user, Project $project): bool
    {
        return $project->userCan($user, 'manageProject');
    }

    public function delete(User $user, Project $project): bool
    {
        return $project->owner_id === $user->id;
    }

    public function manageTasks(User $user, Project $project): bool
    {
        return $project->userCan($user, 'manageTasks');
    }

    public function manageWiki(User $user, Project $project): bool
    {
        return $project->userCan($user, 'manageWiki');
    }

    public function manageMembers(User $user, Project $project): bool
    {
        return $project->userCan($user, 'manageMembers');
    }
}
