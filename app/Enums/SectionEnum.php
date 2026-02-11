<?php

namespace App\Enums;

enum SectionEnum: string
{
    case MAIN = 'main';
    case CUBANDJS = 'cubandjs';
    case CUBANDJS_LIVE_SESSIONS = 'cubandjs_live_sessions';

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
            self::MAIN->value => config('app.name'),
            self::CUBANDJS->value => 'CubanDJS Mix',
            self::CUBANDJS_LIVE_SESSIONS->value => 'CubanDJS Sesiones en vivo',
            default => 'N/A'
        };

    }
}
