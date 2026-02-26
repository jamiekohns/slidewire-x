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

## Runtime Defaults (`defaults`)

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

## Settings Precedence

SlideWire resolves runtime settings using a three-level precedence chain:

```
effective = slide_meta[key] ?? deck_meta[key] ?? config(defaults[key])
```

This means:

1. **Slide-level** attributes (on `<x-slidewire::slide>`) take highest priority.
2. **Deck-level** attributes (on `<x-slidewire::deck>`) apply to all slides unless overridden.
3. **Config defaults** (`config/slidewire.php` -> `defaults`) are the fallback.

Example:

```blade
{{-- Deck sets theme and transition for all slides --}}
<x-slidewire::deck theme="night" transition="fade" auto-slide="5000">

    {{-- Inherits deck: theme=night, transition=fade, auto-slide=5000 --}}
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

- `default`, `black`, `white`, `league`, `beige`, `night`, `serif`, `simple`, `solarized`

Each theme is a structured array with the following keys:

```php
'themes' => [
    'corporate' => [
        'background' => 'bg-slate-900 text-slate-100',
        'highlight_theme' => 'github-dark',
        'title' => ['font' => 'font-sans', 'color' => 'text-slate-100', 'size' => 'text-4xl'],
        'text' => ['font' => 'font-sans', 'color' => 'text-slate-300', 'size' => 'text-lg'],
    ],
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
3. Config default (`defaults.highlight.theme`)

## Font Loading (`fonts`)

Map font family names to their loading strategy. System fonts require no loading; Google Fonts families are loaded automatically via `<link>` tag.

```php
'fonts' => [
    'Inter' => ['source' => 'google', 'weights' => [400, 600, 700]],
    'Georgia' => ['source' => 'system'],
],
```

When a Google Fonts family is configured, SlideWire automatically injects preconnect and stylesheet `<link>` tags into the deck view.

Defaults to an empty array (no custom fonts loaded).

## PDF (`pdf`)

- `format` (e.g. `a4`, `letter`)
- `orientation` (`portrait`, `landscape`)
- `include_notes` (boolean)

## Migration from Previous Versions

### `theme_highlight_map` Removal

The `theme_highlight_map` config key has been removed. Highlight themes are now defined inside each theme's nested structure:

**Before:**

```php
'themes' => [
    'night' => 'bg-slate-900 text-slate-200',
],
'theme_highlight_map' => [
    'night' => 'github-dark',
],
```

**After:**

```php
'themes' => [
    'night' => [
        'background' => 'bg-slate-900 text-slate-200',
        'highlight_theme' => 'github-dark',
        'title' => ['font' => '', 'color' => 'text-slate-200', 'size' => 'text-4xl'],
        'text' => ['font' => '', 'color' => 'text-slate-300', 'size' => 'text-lg'],
    ],
],
```

### `themes` Nested Schema

All theme entries must now be arrays with the required keys: `background`, `highlight_theme`, `title`, and `text`. String-only theme entries are no longer supported.

### Stack to Vertical-Slide Replacement

The `<x-slidewire::stack>` component has been replaced by `<x-slidewire::vertical-slide>`.

**Before:**

```blade
<x-slidewire::stack>
    <x-slidewire::slide>...</x-slidewire::slide>
    <x-slidewire::slide>...</x-slidewire::slide>
</x-slidewire::stack>
```

**After:**

```blade
<x-slidewire::vertical-slide>
    <x-slidewire::slide>...</x-slidewire::slide>
    <x-slidewire::slide>...</x-slidewire::slide>
</x-slidewire::vertical-slide>
```
