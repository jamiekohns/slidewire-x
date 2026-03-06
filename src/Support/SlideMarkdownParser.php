<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\Support;

use Illuminate\Support\Str;

class SlideMarkdownParser
{
    public function __construct(protected CodeHighlighter $highlighter) {}

    /**
     * @return array<int, array{meta: array<string, string>, html: string}>
     */
    public function parse(string $content): array
    {
        [$globalMeta, $body] = $this->extractFrontmatter($content);
        $chunks = explode("\n---\n", $body);

        return collect($chunks)
            ->filter(fn (string $chunk): bool => trim($chunk) !== '')
            ->values()
            ->map(function (string $chunk, int $index) use ($globalMeta): array {
                $slide = $this->parseChunk($chunk);

                if ($index === 0 && $globalMeta !== []) {
                    $slide['meta'] = array_merge($globalMeta, $slide['meta']);
                }

                return $slide;
            })
            ->values()
            ->all();
    }

    /**
     * @return array{meta: array<string, string>, html: string}
     */
    protected function parseChunk(string $chunk): array
    {
        [$meta, $body] = $this->extractFrontmatter($chunk);

        $body = $this->highlighter->replaceCodeBlocks($body);
        $html = Str::markdown($body);

        return [
            'meta' => $meta,
            'html' => $html,
        ];
    }

    /**
     * @return array{0: array<string, string>, 1: string}
     */
    protected function extractFrontmatter(string $chunk): array
    {
        $matches = [];

        if (! preg_match('/^---\n(.*?)\n---\n(.*)$/s', ltrim($chunk), $matches)) {
            return [[], $chunk];
        }

        $meta = [];

        foreach (explode("\n", trim($matches[1])) as $line) {
            $parts = explode(':', $line, 2);

            if (count($parts) === 2) {
                $meta[trim($parts[0])] = trim($parts[1]);
            }
        }

        return [$meta, $matches[2]];
    }
}
