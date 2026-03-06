# Configuration

Main configuration file:

- `config/slidewire.php`

## Presentation Discovery

- `presentation_roots`: array of directories where presentations are discovered.

Default:

```php
'presentation_roots' => [
    resource_path('views/pages/slides'),
],
```

## Slide Settings (`slides`)

- `theme`: default theme preset
- `show_controls`: show/hide bottom control bar
- `show_progress`: show/hide top progress bar
- `show_fullscreen_button`: show/hide fullscreen control
- `keyboard`: keyboard navigation toggle
- `touch`: touch/swipe navigation toggle
- `transition`: default transition
- `transition_duration`: base transition duration in ms
- `transition_speed`: `fast`, `default`, `slow`
- `auto_slide`: global auto-slide delay in ms (`0` disables)
- `auto_slide_pause_on_interaction`: pause timer on manual interaction

Highlighting:

- `highlight.enabled`
- `highlight.theme`
- `highlight.font`
- `highlight.font_size`

## Settings Precedence

SlideWire resolves runtime settings using a three-level precedence chain:

```
effective = slide_meta[key] ?? deck_meta[key] ?? config(slides[key])
```

This means:

1. **Slide-level** attributes (on `<x-slidewire::slide>`) take highest priority.
2. **Deck-level** attributes (on `<x-slidewire::deck>`) apply to all slides unless overridden.
3. **Config slides** (`config/slidewire.php` -> `slides`) are the fallback.

Example:

```blade
{{-- Deck sets theme and transition for all slides --}}
<x-slidewire::deck theme="black" transition="fade" auto-slide="5000">

    {{-- Inherits deck: theme=black, transition=fade, auto-slide=5000 --}}
    <x-slidewire::slide>
        <h2>Inherits Deck</h2>
    </x-slidewire::slide>

    {{-- Overrides: theme=white, transition=zoom, auto-slide=2000 --}}
    <x-slidewire::slide theme="white" transition="zoom" auto-slide="2000">
        <h2>Custom Settings</h2>
    </x-slidewire::slide>

</x-slidewire::deck>
```

### Deck-Level Attributes

The `<x-slidewire::deck>` component accepts the following attributes:

- `theme`, `transition`, `transition-speed`, `transition-duration`
- `auto-slide`, `auto-slide-pause-on-interaction`
- `show-controls`, `show-progress`, `show-fullscreen-button`
- `keyboard`, `touch`
- `highlight-theme`

## Theme Presets (`themes`)

SlideWire ships with presets:

- `default`, `black`, `white`, `aurora`, `sunset`, `neon`, `solarized`

Each theme is configured as a `ThemeConfig` DTO:

```php
use WendellAdriel\\SlideWire\\Support\\ThemeConfig;
use WendellAdriel\\SlideWire\\Support\\ThemeFont;

'themes' => [
    'corporate' => new ThemeConfig(
        background: 'bg-slate-900 text-slate-100',
        highlightTheme: 'github-dark',
        title: new ThemeFont('font-sans', 'text-slate-100', 'text-4xl'),
        text: new ThemeFont('font-sans', 'text-slate-300', 'text-lg'),
    ),
],
```

### Theme Keys

| Key | Description |
|-----|-------------|
| `background` | Tailwind classes applied to the deck shell (background color, text color) |
| `highlight_theme` | Syntax highlighting theme name for code blocks |
| `title.font` | Tailwind font-family class for headings |
| `title.color` | Tailwind text color class for headings |
| `title.size` | Tailwind text size class for headings |
| `text.font` | Tailwind font-family class for body text |
| `text.color` | Tailwind text color class for body text |
| `text.size` | Tailwind text size class for body text |

Then use on a slide:

```blade
<x-slidewire::slide theme="corporate">
    <h2>Quarterly Review</h2>
</x-slidewire::slide>
```

### Highlight Theme Resolution

The highlight theme for code blocks is resolved from the active theme's `highlight_theme` key.

Resolution order:

1. Explicit `highlight-theme` attribute on slide or deck
2. Active theme's `highlight_theme` from config
3. Config default (`slides.highlight.theme`)

## Font Loading (`fonts`)

Map font family names to their loading strategy. System fonts require no loading; Google Fonts families are loaded automatically via `<link>` tag.

```php
use WendellAdriel\\SlideWire\\Support\\FontConfig;
use WendellAdriel\\SlideWire\\Support\\FontSource;

'slides' => [
    'highlight' => [
        'enabled' => true,
        'theme' => 'catppuccin-mocha',
        'font' => 'JetBrainsMono',
        'font_size' => 'md',
    ],
],

'fonts' => [
    'Inter' => new FontConfig(FontSource::Google, [400, 600, 700]),
    'JetBrainsMono' => new FontConfig(FontSource::Google, [400, 700]),
    'Georgia' => new FontConfig(FontSource::System),
],
```

When a Google Fonts family is configured, SlideWire automatically injects preconnect and stylesheet `<link>` tags into the deck view. Code blocks use `slides.highlight.font` and `slides.highlight.font_size` by default, and both `<x-slidewire::code>` and `<x-slidewire::markdown>` can override the size with a `size` attribute. The `size` value accepts raw CSS sizes or the aliases `xs`, `sm`, `md`, `lg`, `xl`, and `2xl`.

Defaults to an empty array (no custom fonts loaded).

## PDF Export

PDF export is handled by the `slidewire:pdf` Artisan command and defaults to A4 landscape.
Override via command options `--format=` and `--orientation=`.
