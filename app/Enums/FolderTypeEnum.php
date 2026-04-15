<?php

namespace App\Enums;

enum FolderTypeEnum: string
{
    case PLAYLIST = 'playlists';
    case PACKS = 'packs';

    public static function getValues()
    {
        return array_map(
            fn($item) => $item->value,
            static::cases()
        );
    }

    public static function getTransformName($name)
    {

        return match ($name) {
            self::PLAYLIST->value => 'Playlists',
            self::PACKS->value => 'Packs',
            default => 'N/A'
        };

    }

    public static function getTransformColor($name)
    {

        return match ($name) {
            self::PLAYLIST->value => 'primary',
            self::PACKS->value => 'info',
            default => 'N/A'
        };

    }
}
