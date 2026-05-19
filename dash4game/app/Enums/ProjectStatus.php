<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case Idea          = 'idea';
    case PreProduction = 'pre_production';
    case Production    = 'production';
    case Testing       = 'testing';
    case Released      = 'released';
    case Paused        = 'paused';

    public function label(): string
    {
        return match($this) {
            self::Idea          => 'Idea',
            self::PreProduction => 'Pre-Production',
            self::Production    => 'Production',
            self::Testing       => 'Testing',
            self::Released      => 'Released',
            self::Paused        => 'Paused',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Idea          => 'gray',
            self::PreProduction => 'blue',
            self::Production    => 'amber',
            self::Testing       => 'purple',
            self::Released      => 'green',
            self::Paused        => 'red',
        };
    }
}
