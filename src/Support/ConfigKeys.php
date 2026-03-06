<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\Support;

/**
 * Centralized config-key constants to avoid scattered string keys across the codebase.
 */
final class ConfigKeys
{
    public const string ROOT = 'slidewire';

    public const string PRESENTATION_ROOTS = 'slidewire.presentation_roots';

    public const string SLIDES = 'slidewire.slides';

    public const string SLIDES_THEME = 'slidewire.slides.theme';

    public const string SLIDES_SHOW_CONTROLS = 'slidewire.slides.show_controls';

    public const string SLIDES_SHOW_PROGRESS = 'slidewire.slides.show_progress';

    public const string SLIDES_SHOW_FULLSCREEN_BUTTON = 'slidewire.slides.show_fullscreen_button';

    public const string SLIDES_KEYBOARD = 'slidewire.slides.keyboard';

    public const string SLIDES_TOUCH = 'slidewire.slides.touch';

    public const string SLIDES_TRANSITION = 'slidewire.slides.transition';

    public const string SLIDES_TRANSITION_DURATION = 'slidewire.slides.transition_duration';

    public const string SLIDES_TRANSITION_SPEED = 'slidewire.slides.transition_speed';

    public const string SLIDES_AUTO_SLIDE = 'slidewire.slides.auto_slide';

    public const string SLIDES_AUTO_SLIDE_PAUSE = 'slidewire.slides.auto_slide_pause_on_interaction';

    public const string SLIDES_HIGHLIGHT_ENABLED = 'slidewire.slides.highlight.enabled';

    public const string SLIDES_HIGHLIGHT_THEME = 'slidewire.slides.highlight.theme';

    public const string SLIDES_HIGHLIGHT_FONT = 'slidewire.slides.highlight.font';

    public const string SLIDES_HIGHLIGHT_FONT_SIZE = 'slidewire.slides.highlight.font_size';

    public const string THEMES = 'slidewire.themes';

    public const string FONTS = 'slidewire.fonts';

    /**
     * Required keys for each theme entry in the nested schema.
     *
     * @var list<string>
     */
    public const array THEME_REQUIRED_KEYS = ['background', 'highlight_theme', 'title', 'text'];

    /**
     * Required keys for theme typography entries (title, text).
     *
     * @var list<string>
     */
    public const array TYPOGRAPHY_REQUIRED_KEYS = ['font', 'color', 'size'];
}
