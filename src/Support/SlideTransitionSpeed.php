<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\Support;

enum SlideTransitionSpeed: string
{
    case Fast = 'fast';
    case Default = 'default';
    case Slow = 'slow';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $speed): string => $speed->value, self::cases());
    }
}
