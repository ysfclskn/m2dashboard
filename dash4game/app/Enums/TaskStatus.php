<?php

namespace App\Enums;

enum TaskStatus: string
{
    case Backlog    = 'backlog';
    case Todo       = 'todo';
    case InProgress = 'in_progress';
    case Review     = 'review';
    case Done       = 'done';

    public function label(): string
    {
        return match($this) {
            self::Backlog    => 'Quest Log',
            self::Todo       => 'Ready',
            self::InProgress => 'In Progress',
            self::Review     => 'Review',
            self::Done       => 'Completed',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Backlog    => 'gray',
            self::Todo       => 'blue',
            self::InProgress => 'yellow',
            self::Review     => 'purple',
            self::Done       => 'green',
        };
    }

    public static function kanbanColumns(): array
    {
        return [self::Todo, self::InProgress, self::Review, self::Done];
    }
}
