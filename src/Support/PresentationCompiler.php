<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\Support;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Throwable;

class PresentationCompiler
{
    public function __construct(protected PresentationPathResolver $resolver) {}

    /**
     * Compile a presentation and return a structured payload with deck metadata and slides.
     *
     * The slides array represents a 2D grid. Each top-level entry is a horizontal column.
     * If a column contains a stack, it has multiple vertical slides. Otherwise, it's a
     * single-slide column.
     *
     * @return array{deck_meta: array<string, string>, slides: array<int, array<int, array{id: string, html: string, meta: array<string, string>, fragments: int, class: string}>>}
     *
     * @throws RuntimeException when the presentation file cannot be read or rendered
     */
    public function compile(string $presentation): array
    {
        $path = $this->resolver->presentationPath($presentation);

        if ($path === null) {
            return ['deck_meta' => [], 'slides' => []];
        }

        return $this->compileFile($path);
    }

    /**
     * Flatten a 2D slide grid into a linear list suitable for PDF export.
     *
     * @param  array<int, array<int, array{id: string, html: string, meta: array<string, string>, fragments: int, class: string}>>  $columns
     * @return array<int, array{id: string, html: string, meta: array<string, string>, fragments: int, class: string}>
     */
    public function flattenSlides(array $columns): array
    {
        $flat = [];

        foreach ($columns as $column) {
            foreach ($column as $slide) {
                $flat[] = $slide;
            }
        }

        return $flat;
    }

    /**
     * @return array{deck_meta: array<string, string>, slides: array<int, array<int, array{id: string, html: string, meta: array<string, string>, fragments: int, class: string}>>}
     *
     * @throws RuntimeException when the file cannot be read or rendered
     */
    protected function compileFile(string $path): array
    {
        if (! File::exists($path)) {
            throw new RuntimeException("Presentation file not found: {$path}");
        }

        try {
            $content = File::get($path);
        } catch (Throwable $e) {
            throw new RuntimeException("Failed to read presentation file [{$path}]: {$e->getMessage()}", $e->getCode(), previous: $e);
        }

        try {
            $html = Blade::render($content, [], deleteCachedView: true);
        } catch (Throwable $e) {
            throw new RuntimeException("Failed to render presentation [{$path}]: {$e->getMessage()}", $e->getCode(), previous: $e);
        }

        $deckMeta = $this->extractDeckMeta($html);

        // Extract the inner HTML of the slidewire-deck section
        $deckInner = $this->extractDeckInner($html);

        if ($deckInner === null) {
            // Fallback: try parsing article tags directly from the full HTML
            return ['deck_meta' => $deckMeta, 'slides' => $this->parseFlatSlides($html, $path)];
        }

        return ['deck_meta' => $deckMeta, 'slides' => $this->parseStructuredSlides($deckInner, $path)];
    }

    /**
     * Extract inner HTML of the <section class="slidewire-deck"> wrapper.
     */
    protected function extractDeckInner(string $html): ?string
    {
        if (preg_match('/<section\b[^>]*class="[^"]*slidewire-deck[^"]*"[^>]*>(.*)<\/section>/is', $html, $match) !== 1) {
            return null;
        }

        return $match[1];
    }

    /**
     * Parse structured slides with vertical-slide support. Top-level children of the deck
     * are either <article> (single slide) or <section class="slidewire-vertical-slide"> (vertical group).
     *
     * @return array<int, array<int, array{id: string, html: string, meta: array<string, string>, fragments: int, class: string}>>
     */
    protected function parseStructuredSlides(string $deckInner, string $path): array
    {
        $columns = [];
        $colIndex = 0;

        // Match top-level sections (vertical-slide groups) and articles
        // We need to process them in document order
        $offset = 0;
        $len = strlen($deckInner);

        while ($offset < $len) {
            // Find the next tag of interest -- support both new and legacy class names
            $nextVertical = $this->findTag($deckInner, 'section', $offset, 'slidewire-vertical-slide');
            $nextStack = $this->findTag($deckInner, 'section', $offset, 'slidewire-stack');
            $nextArticle = $this->findTag($deckInner, 'article', $offset);

            // Choose the earliest vertical group match (vertical-slide or legacy stack)
            if ($nextVertical !== null && $nextStack !== null) {
                $nextStack = $nextVertical['start'] <= $nextStack['start'] ? $nextVertical : $nextStack;
            } elseif ($nextVertical !== null) {
                $nextStack = $nextVertical;
            }

            if ($nextStack === null && $nextArticle === null) {
                break;
            }

            // Determine which comes first
            $stackPos = $nextStack !== null ? $nextStack['start'] : PHP_INT_MAX;
            $articlePos = $nextArticle !== null ? $nextArticle['start'] : PHP_INT_MAX;

            if ($stackPos < $articlePos) {
                // Process stack: extract all articles within it
                $stackArticles = [];
                preg_match_all('/<article\b([^>]*)>(.*?)<\/article>/is', $nextStack['inner'], $articles, PREG_SET_ORDER);

                foreach ($articles as $vIndex => $article) {
                    $stackArticles[] = $this->buildSlide($article, $path, $colIndex, $vIndex);
                }

                if ($stackArticles !== []) {
                    $columns[] = $stackArticles;
                }

                $offset = $nextStack['end'];
                ++$colIndex;
            } else {
                // Process single article
                $columns[] = [$this->buildSlide($nextArticle['match'], $path, $colIndex, 0)];
                $offset = $nextArticle['end'];
                ++$colIndex;
            }
        }

        return $columns;
    }

    /**
     * Fallback: parse flat slides (no stack awareness).
     *
     * @return array<int, array<int, array{id: string, html: string, meta: array<string, string>, fragments: int, class: string}>>
     */
    protected function parseFlatSlides(string $html, string $path): array
    {
        preg_match_all('/<article\b([^>]*)>(.*?)<\/article>/is', $html, $slides, PREG_SET_ORDER);

        if ($slides === []) {
            return [];
        }

        return array_values(array_map(
            fn (array $slide, int $index): array => [$this->buildSlide($slide, $path, $index, 0)],
            $slides,
            array_keys($slides),
        ));
    }

    /**
     * @return array{id: string, html: string, meta: array<string, string>, fragments: int, class: string}
     */
    protected function buildSlide(array $match, string $path, int $hIndex, int $vIndex): array
    {
        $attributes = $this->extractAttributes($match[1] ?? '');
        $innerHtml = trim($match[2] ?? '');

        return [
            'id' => $this->slideId($path, $hIndex, $vIndex),
            'html' => $innerHtml,
            'meta' => $this->extractMetaFromAttributes($attributes),
            'fragments' => $this->fragmentCount($innerHtml),
            'class' => $attributes['class'] ?? '',
        ];
    }

    /**
     * Find the next occurrence of a tag, optionally requiring a specific class.
     *
     * @return array{start: int, end: int, inner: string, match: array}|null
     */
    protected function findTag(string $html, string $tag, int $offset, ?string $requiredClass = null): ?array
    {
        $pattern = $requiredClass !== null
            ? '/<' . $tag . '\b([^>]*class="[^"]*' . preg_quote($requiredClass, '/') . '[^"]*"[^>]*)>(.*?)<\/' . $tag . '>/is'
            : '/<' . $tag . '\b([^>]*)>(.*?)<\/' . $tag . '>/is';

        if (preg_match($pattern, $html, $match, PREG_OFFSET_CAPTURE, $offset) !== 1) {
            return null;
        }

        return [
            'start' => $match[0][1],
            'end' => $match[0][1] + strlen($match[0][0]),
            'inner' => $match[2][0],
            'match' => [$match[0][0], $match[1][0], $match[2][0]],
        ];
    }

    /**
     * Extract deck-level metadata from the <section class="slidewire-deck"> wrapper.
     *
     * @return array<string, string>
     */
    protected function extractDeckMeta(string $html): array
    {
        if (preg_match('/<section\b([^>]*class="[^"]*slidewire-deck[^"]*"[^>]*)>/i', $html, $match) !== 1) {
            return [];
        }

        $attributes = $this->extractAttributes($match[1]);

        return $this->extractMetaFromAttributes($attributes);
    }

    /**
     * @return array<string, string>
     */
    protected function extractMetaFromAttributes(array $attributes): array
    {
        $meta = [];

        foreach ($attributes as $key => $value) {
            if (str_starts_with((string) $key, 'data-')) {
                $normalizedKey = str_replace(['data-', '-'], ['', '_'], strtolower((string) $key));
                $meta[$normalizedKey] = $value;
            }
        }

        return $meta;
    }

    /**
     * @return array<string, string>
     */
    protected function extractAttributes(string $rawAttributes): array
    {
        preg_match_all('/([\w-]+)\s*=\s*("|\')(.*?)\2/s', $rawAttributes, $matches, PREG_SET_ORDER);

        if ($matches === []) {
            return [];
        }

        $attributes = [];

        foreach ($matches as $attribute) {
            $attributes[strtolower($attribute[1])] = html_entity_decode($attribute[3], ENT_QUOTES);
        }

        return $attributes;
    }

    protected function fragmentCount(string $html): int
    {
        preg_match_all('/data-fragment(?:-index)?="?(\d+)?"?/', $html, $matches);

        if ($matches[0] === []) {
            return 0;
        }

        $indices = array_filter($matches[1], fn (string $value): bool => $value !== '');

        if ($indices === []) {
            return count($matches[0]);
        }

        return max(array_map(intval(...), $indices)) + 1;
    }

    protected function slideId(string $path, int $hIndex, int $vIndex): string
    {
        $basename = pathinfo($path, PATHINFO_BASENAME);
        $name = str_ends_with($basename, '.blade.php')
            ? substr($basename, 0, -10)
            : pathinfo($path, PATHINFO_FILENAME);

        if ($vIndex > 0) {
            return $name . '-' . $hIndex . '-' . $vIndex;
        }

        return $name . '-' . $hIndex;
    }
}
