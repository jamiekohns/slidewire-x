<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\Support;

use Illuminate\Support\HtmlString;
use Phiki\Phiki;
use Throwable;

class CodeHighlighter
{
    /**
     * Highlight code using the given (or resolved) highlight theme.
     *
     * Theme resolution order:
     *   1. Explicit $highlightTheme parameter
     *   2. Theme-to-highlight mapping for the given $presentationTheme
     *   3. Config default (slidewire.defaults.highlight.theme)
     */
    public function highlight(string $code, string $language, ?string $highlightTheme = null, ?string $presentationTheme = null): HtmlString
    {
        if (! config('slidewire.defaults.highlight.enabled', true)) {
            return $this->fallback($code, $language);
        }

        $resolvedTheme = $this->resolveHighlightTheme($highlightTheme, $presentationTheme);

        $highlighted = $this->attemptPhikiHighlight($code, $language, $resolvedTheme);

        if ($highlighted !== null) {
            return new HtmlString($highlighted);
        }

        return $this->fallback($code, $language);
    }

    /**
     * Resolve highlight theme using precedence: explicit > theme config > config default.
     */
    public function resolveHighlightTheme(?string $highlightTheme = null, ?string $presentationTheme = null): string
    {
        if ($highlightTheme !== null && $highlightTheme !== '') {
            return $highlightTheme;
        }

        if ($presentationTheme !== null && $presentationTheme !== '') {
            $themeHighlight = config("slidewire.themes.{$presentationTheme}.highlight_theme");

            if (is_string($themeHighlight) && $themeHighlight !== '') {
                return $themeHighlight;
            }
        }

        return (string) config('slidewire.defaults.highlight.theme', 'github-dark');
    }

    /**
     * Replace fenced code blocks (```lang\n...\n```) in markdown with highlighted HTML.
     *
     * This is the single shared path used by both the markdown parser and the Markdown Blade component,
     * ensuring consistent highlighting behavior.
     */
    public function replaceCodeBlocks(string $markdown, ?string $highlightTheme = null, ?string $presentationTheme = null): string
    {
        return (string) preg_replace_callback('/```([\w-]*)\n(.*?)```/s', function (array $matches) use ($highlightTheme, $presentationTheme): string {
            $language = $matches[1] !== '' ? $matches[1] : 'text';
            $code = rtrim($matches[2]);

            return $this->highlight($code, $language, $highlightTheme, $presentationTheme)->toHtml();
        }, $markdown);
    }

    protected function attemptPhikiHighlight(string $code, string $language, string $theme): ?string
    {
        if (! class_exists(Phiki::class)) {
            return null;
        }

        try {
            $phiki = new Phiki();
            $result = $phiki->codeToHtml($code, $language, $theme)->toString();

            return is_string($result) ? $result : null;
        } catch (Throwable) {
            return null;
        }
    }

    protected function fallback(string $code, string $language): HtmlString
    {
        return new HtmlString(
            '<pre class="slidewire-code"><code class="language-' . e($language) . '">' . e($code) . '</code></pre>'
        );
    }
}
