<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\Support;

final class DatabaseDocumentKey
{
    public static function idFromToken(?string $token): ?int
    {
        if ($token === null || $token === '') {
            return null;
        }

        if (preg_match('/^(?<id>\d+)(?:-.+)?$/', $token, $matches) !== 1) {
            return null;
        }

        return (int) $matches['id'];
    }
}
