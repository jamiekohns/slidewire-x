<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\Enums;

enum SlideTransition: string
{
    case Slide = 'slide';
    case Fade = 'fade';
    case Zoom = 'zoom';
    case Convex = 'convex';
    case Concave = 'concave';
    case None = 'none';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $transition): string => $transition->value, self::cases());
    }
}
