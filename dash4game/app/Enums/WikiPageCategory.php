<?php

namespace App\Enums;

enum WikiPageCategory: string
{
    case GameDesign     = 'game_design';
    case StoryLore      = 'story_lore';
    case Characters     = 'characters';
    case Mechanics      = 'mechanics';
    case TechnicalNotes = 'technical_notes';
    case ArtDirection   = 'art_direction';
    case Audio          = 'audio';
    case Marketing      = 'marketing';
    case Other          = 'other';

    public function label(): string
    {
        return match($this) {
            self::GameDesign     => 'Game Design',
            self::StoryLore      => 'Story & Lore',
            self::Characters     => 'Characters',
            self::Mechanics      => 'Mechanics',
            self::TechnicalNotes => 'Technical Notes',
            self::ArtDirection   => 'Art Direction',
            self::Audio          => 'Audio',
            self::Marketing      => 'Marketing',
            self::Other          => 'Other',
        };
    }
}
