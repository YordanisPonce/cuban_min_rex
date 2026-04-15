<?php

namespace App\Enums;

enum NotificationTypeEnum: string
{
    case SYSTEM = 'system';
    case SOCIAL = 'social';
    case PROMOTIONAL = 'promotional';
    case REMIXES = 'remixes';
    case BUY = 'buy';

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
            self::SYSTEM->value => 'Sistema',
            self::SOCIAL->value => 'Social',
            self::PROMOTIONAL->value => 'Promocional',
            self::REMIXES->value => 'Remixes',
            self::BUY->value => 'Compras',
            default => 'N/A'
        };

    }
}
