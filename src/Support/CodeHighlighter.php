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
     *   3. Config default (slidewire.slides.highlight.theme)
     */
    public function highlight(string $code, string $language, ?string $highlightTheme = null, ?string $presentationTheme = null, ?string $font = null): HtmlString
    {
        if (! config(ConfigKeys::SLIDES_HIGHLIGHT_ENABLED, true)) {
            return $this->fallback($code, $language, $font);
        }

        $resolvedTheme = $this->resolveHighlightTheme($highlightTheme, $presentationTheme);

        $highlighted = $this->attemptPhikiHighlight($code, $language, $resolvedTheme, $font);

        if ($highlighted !== null) {
            return new HtmlString($highlighted);
        }

        return $this->fallback($code, $language, $font);
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
            $themeConfig = config(ConfigKeys::THEMES, [])[$presentationTheme] ?? null;

            if ($themeConfig instanceof ThemeConfig && $themeConfig->highlightTheme !== '') {
                return $themeConfig->highlightTheme;
            }

            if (is_array($themeConfig)) {
                $themeHighlight = $themeConfig['highlight_theme'] ?? null;

                if (is_string($themeHighlight) && $themeHighlight !== '') {
                    return $themeHighlight;
                }
            }
        }

        return (string) config(ConfigKeys::SLIDES_HIGHLIGHT_THEME, 'github-dark');
    }

    /**
     * Replace fenced code blocks (```lang\n...\n```) in markdown with highlighted HTML.
     *
     * This is the single shared path used by both the markdown parser and the Markdown Blade component,
     * ensuring consistent highlighting behavior.
     */
    public function replaceCodeBlocks(string $markdown, ?string $highlightTheme = null, ?string $presentationTheme = null, ?string $font = null): string
    {
        return (string) preg_replace_callback('/```([\w-]*)\n(.*?)```/s', function (array $matches) use ($highlightTheme, $presentationTheme, $font): string {
            $language = $matches[1] !== '' ? $matches[1] : 'text';
            $code = rtrim($matches[2]);

            return $this->highlight($code, $language, $highlightTheme, $presentationTheme, $font)->toHtml();
        }, $markdown);
    }

    protected function attemptPhikiHighlight(string $code, string $language, string $theme, ?string $font = null): ?string
    {
        if (! class_exists(Phiki::class)) {
            return null;
        }

        try {
            $phiki = new Phiki();
            $result = $phiki->codeToHtml($code, $language, $theme)->toString();

            if (! is_string($result)) {
                return null;
            }

            return $this->applyFontFamily($result, $font);
        } catch (Throwable) {
            return null;
        }
    }

    protected function fallback(string $code, string $language, ?string $font = null): HtmlString
    {
        $style = $this->fontStyleAttribute($font);

        return new HtmlString(
            '<pre class="slidewire-code"' . $style . '><code class="language-' . e($language) . '"' . $style . '>' . e($code) . '</code></pre>'
        );
    }

    protected function applyFontFamily(string $html, ?string $font = null): string
    {
        $style = $this->fontStyleAttribute($font);

        if ($style === '') {
            return $html;
        }

        $html = preg_replace('/<pre([^>]*)>/', '<pre$1' . $style . '>', $html, 1);

        return is_string($html)
            ? (string) preg_replace('/<code([^>]*)>/', '<code$1' . $style . '>', $html, 1)
            : '';
    }

    protected function fontStyleAttribute(?string $font = null): string
    {
        $font = trim((string) ($font ?? config(ConfigKeys::SLIDES_HIGHLIGHT_FONT, '')));

        if ($font === '') {
            return '';
        }

        $fallback = "ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace";

        return ' style="font-family: ' . e("'{$font}', {$fallback}") . '"';
    }
}
