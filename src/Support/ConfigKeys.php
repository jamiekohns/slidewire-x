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

    public const string DEFAULTS = 'slidewire.defaults';

    public const string DEFAULTS_THEME = 'slidewire.defaults.theme';

    public const string DEFAULTS_SHOW_CONTROLS = 'slidewire.defaults.show_controls';

    public const string DEFAULTS_SHOW_PROGRESS = 'slidewire.defaults.show_progress';

    public const string DEFAULTS_SHOW_FULLSCREEN_BUTTON = 'slidewire.defaults.show_fullscreen_button';

    public const string DEFAULTS_KEYBOARD = 'slidewire.defaults.keyboard';

    public const string DEFAULTS_TOUCH = 'slidewire.defaults.touch';

    public const string DEFAULTS_TRANSITION = 'slidewire.defaults.transition';

    public const string DEFAULTS_TRANSITION_DURATION = 'slidewire.defaults.transition_duration';

    public const string DEFAULTS_TRANSITION_SPEED = 'slidewire.defaults.transition_speed';

    public const string DEFAULTS_AUTO_SLIDE = 'slidewire.defaults.auto_slide';

    public const string DEFAULTS_AUTO_SLIDE_PAUSE = 'slidewire.defaults.auto_slide_pause_on_interaction';

    public const string DEFAULTS_MARKDOWN_SEPARATOR = 'slidewire.defaults.markdown.separator';

    public const string DEFAULTS_HIGHLIGHT_ENABLED = 'slidewire.defaults.highlight.enabled';

    public const string DEFAULTS_HIGHLIGHT_THEME = 'slidewire.defaults.highlight.theme';

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
