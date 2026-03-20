<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use WendellAdriel\SlideWire\Contracts\DatabaseDocumentProvider;
use WendellAdriel\SlideWire\DTOs\DatabaseDocument;

final class FakeDatabaseDocumentProvider implements DatabaseDocumentProvider
{
    /** @var array<int, DatabaseDocument> */
    private static array $documents = [];

    /**
     * @param  array<int, DatabaseDocument>  $documents
     */
    public static function seed(array $documents): void
    {
        self::$documents = [];

        foreach ($documents as $document) {
            self::$documents[$document->id] = $document;
        }
    }

    public function findById(int $id): ?DatabaseDocument
    {
        return self::$documents[$id] ?? null;
    }
}
