<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\Support;

use Illuminate\Support\HtmlString;
use Phiki\Phiki;
use Phiki\Theme\Theme;
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
    public function highlight(string $code, string $language, Theme|string|null $highlightTheme = null, ?string $presentationTheme = null, ?string $font = null, ?string $size = null): HtmlString
    {
        $slides = config('slidewire.slides', new SlidesConfig());

        if (! $slides->highlight->enabled) {
            return $this->fallback($code, $language, $font, $size);
        }

        $resolvedTheme = $this->resolveHighlightTheme($highlightTheme, $presentationTheme);

        $highlighted = $this->attemptPhikiHighlight($code, $language, $resolvedTheme, $font, $size);

        if ($highlighted !== null) {
            return new HtmlString($highlighted);
        }

        return $this->fallback($code, $language, $font, $size);
    }

    /**
     * Resolve highlight theme using precedence: explicit > theme config > config default.
     */
    public function resolveHighlightTheme(Theme|string|null $highlightTheme = null, ?string $presentationTheme = null): Theme
    {
        $explicitTheme = $this->normalizeTheme($highlightTheme);

        if ($explicitTheme instanceof Theme) {
            return $explicitTheme;
        }

        if ($presentationTheme !== null && $presentationTheme !== '') {
            $themeConfig = config('slidewire.themes', [])[$presentationTheme] ?? null;

            if ($themeConfig instanceof ThemeConfig) {
                return $themeConfig->highlightTheme;
            }
        }

        return config('slidewire.slides', new SlidesConfig())->highlight->theme;
    }

    /**
     * Replace fenced code blocks (```lang\n...\n```) in markdown with highlighted HTML.
     *
     * This is the single shared path used by both the markdown parser and the Markdown Blade component,
     * ensuring consistent highlighting behavior.
     */
    public function replaceCodeBlocks(string $markdown, Theme|string|null $highlightTheme = null, ?string $presentationTheme = null, ?string $font = null, ?string $size = null): string
    {
        return (string) preg_replace_callback('/```([\w-]*)\n(.*?)```/s', function (array $matches) use ($highlightTheme, $presentationTheme, $font, $size): string {
            $language = $matches[1] !== '' ? $matches[1] : 'text';
            $code = rtrim($matches[2]);

            return $this->highlight($code, $language, $highlightTheme, $presentationTheme, $font, $size)->toHtml();
        }, $markdown);
    }

    protected function attemptPhikiHighlight(string $code, string $language, Theme $theme, ?string $font = null, ?string $size = null): ?string
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

            return $this->applyCodeStyles($result, $font, $size);
        } catch (Throwable) {
            return null;
        }
    }

    protected function fallback(string $code, string $language, ?string $font = null, ?string $size = null): HtmlString
    {
        $style = $this->styleAttribute($font, $size);

        return new HtmlString(
            '<pre class="slidewire-code"' . $style . '><code class="language-' . e($language) . '"' . $style . '>' . e($code) . '</code></pre>'
        );
    }

    protected function applyCodeStyles(string $html, ?string $font = null, ?string $size = null): string
    {
        $style = $this->styleAttribute($font, $size);

        if ($style === '') {
            return $html;
        }

        $html = $this->mergeStyleAttribute($html, 'pre', $style);

        return $this->mergeStyleAttribute($html, 'code', $style);
    }

    protected function styleAttribute(?string $font = null, ?string $size = null): string
    {
        $highlight = config('slidewire.slides', new SlidesConfig())->highlight;
        $font = trim((string) ($font ?? $highlight->font));
        $size = trim((string) ($size ?? $highlight->fontSize));

        $styles = [];

        if ($font !== '') {
            $fallback = "ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace";
            $styles[] = 'font-family: ' . e("'{$font}', {$fallback}");
        }

        if ($size !== '') {
            $styles[] = 'font-size: ' . e($this->normalizeFontSize($size));
        }

        if ($styles === []) {
            return '';
        }

        return ' style="' . implode('; ', $styles) . '"';
    }

    protected function normalizeFontSize(string $size): string
    {
        return match (trim($size)) {
            'xs' => '0.75rem',
            'sm' => '0.875rem',
            'md' => '1rem',
            'lg' => '1.125rem',
            'xl' => '1.25rem',
            '2xl' => '1.5rem',
            default => trim($size),
        };
    }

    protected function mergeStyleAttribute(string $html, string $tag, string $style): string
    {
        $styleValue = trim((string) preg_replace('/^ style="(.*)"$/', '$1', $style));

        if ($styleValue === '') {
            return $html;
        }

        $updated = preg_replace_callback(
            '/<' . $tag . '([^>]*)>/i',
            static function (array $matches) use ($tag, $styleValue): string {
                $attributes = $matches[1];

                if (preg_match('/\sstyle="([^"]*)"/i', $attributes, $styleMatch) === 1) {
                    $mergedStyles = rtrim(trim($styleMatch[1]), ';');

                    if ($mergedStyles !== '') {
                        $mergedStyles .= '; ';
                    }

                    $mergedStyles .= $styleValue;
                    $attributes = preg_replace('/\sstyle="[^"]*"/i', ' style="' . $mergedStyles . '"', $attributes, 1);

                    return '<' . $tag . $attributes . '>';
                }

                return '<' . $tag . $attributes . ' style="' . $styleValue . '">';
            },
            $html,
            1,
        );

        return is_string($updated) ? $updated : $html;
    }

    protected function normalizeTheme(Theme|string|null $theme): ?Theme
    {
        if ($theme instanceof Theme) {
            return $theme;
        }

        if (! is_string($theme) || trim($theme) === '') {
            return null;
        }

        return Theme::tryFrom(trim($theme));
    }
}
