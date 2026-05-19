<?php

namespace App\Enums;

enum ProjectRole: string
{
    case Owner  = 'owner';
    case Admin  = 'admin';
    case Member = 'member';
    case Viewer = 'viewer';

    public function label(): string
    {
        return match($this) {
            self::Owner  => 'Guild Master',
            self::Admin  => 'Raid Leader',
            self::Member => 'Adventurer',
            self::Viewer => 'Observer',
        };
    }

    public function canManageProject(): bool
    {
        return in_array($this, [self::Owner, self::Admin]);
    }

    public function canManageTasks(): bool
    {
        return in_array($this, [self::Owner, self::Admin, self::Member]);
    }

    public function canManageWiki(): bool
    {
        return in_array($this, [self::Owner, self::Admin, self::Member]);
    }

    public function canManageMembers(): bool
    {
        return in_array($this, [self::Owner, self::Admin]);
    }
}
