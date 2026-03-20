<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\Contracts;

use WendellAdriel\SlideWire\DTOs\DatabaseDocument;

interface DatabaseDocumentProvider
{
    public function findById(int $id): ?DatabaseDocument;
}
