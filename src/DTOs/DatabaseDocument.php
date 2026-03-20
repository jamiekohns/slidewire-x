<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\DTOs;

final readonly class DatabaseDocument
{
    public function __construct(
        public int $id,
        public string $name,
        public string $content,
        public ?int $ownerId = null,
        public ?string $customCss = null,
    ) {}
}
