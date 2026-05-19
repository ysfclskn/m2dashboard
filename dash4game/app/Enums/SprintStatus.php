<?php

namespace App\Enums;

enum SprintStatus: string
{
    case Planned   = 'planned';
    case Active    = 'active';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Planned   => 'Planned',
            self::Active    => 'Active Raid',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Planned   => 'gray',
            self::Active    => 'green',
            self::Completed => 'blue',
            self::Cancelled => 'red',
        };
    }
}
