<?php

namespace App\Enums;

enum TaskType: string
{
    case Feature   = 'feature';
    case Bug       = 'bug';
    case Art       = 'art';
    case Design    = 'design';
    case Sound     = 'sound';
    case Story     = 'story';
    case Technical = 'technical';

    public function label(): string
    {
        return match($this) {
            self::Feature   => 'Feature',
            self::Bug       => 'Bug Hunt',
            self::Art       => 'Art',
            self::Design    => 'Design',
            self::Sound     => 'Sound',
            self::Story     => 'Story',
            self::Technical => 'Technical',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Feature   => 'blue',
            self::Bug       => 'red',
            self::Art       => 'pink',
            self::Design    => 'purple',
            self::Sound     => 'cyan',
            self::Story     => 'amber',
            self::Technical => 'gray',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::Feature   => '⚔️',
            self::Bug       => '🐛',
            self::Art       => '🎨',
            self::Design    => '✏️',
            self::Sound     => '🎵',
            self::Story     => '📖',
            self::Technical => '⚙️',
        };
    }
}
