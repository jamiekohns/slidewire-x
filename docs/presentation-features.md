# Presentation Features

## Navigation and Presentation Controls

SlideWire decks support:

- keyboard navigation (`ArrowLeft`, `ArrowRight`, `ArrowUp`, `ArrowDown`, `Space`)
- click/tap navigation
- swipe navigation on touch devices (horizontal and vertical)
- hash deep-linking (`#/slide/N` for horizontal, `#/slide/H/V` for vertical)
- fullscreen toggle from controls bar
- directional arrow controls with disabled states

## Vertical Slides

Wrap slides in `<x-slidewire::vertical-slide>` to create vertical columns:

```blade
<x-slidewire::deck>
    <x-slidewire::slide>
        <h2>Slide 1</h2>
    </x-slidewire::slide>

    <x-slidewire::vertical-slide>
        <x-slidewire::slide>
            <h2>Column 2 - Top</h2>
        </x-slidewire::slide>
        <x-slidewire::slide>
            <h2>Column 2 - Bottom</h2>
        </x-slidewire::slide>
    </x-slidewire::vertical-slide>
</x-slidewire::deck>
```

Arrow keys navigate in 2D:

- `Left` / `Right` move between horizontal columns (landing at vertical index 0)
- `Up` / `Down` move within a vertical group
- `Space` advances linearly through all slides

The controls bar shows directional arrows. Up/down arrows appear only when the deck contains vertical groups. Arrows are disabled when at a navigation boundary.

## Transitions

Supported transition names:

- `slide`
- `fade`
- `zoom`
- `convex`
- `concave`
- `none`

Example:

```blade
<x-slidewire::slide transition="zoom">
    <h2>Zoom</h2>
</x-slidewire::slide>
```

Vertical transitions automatically use the Y axis for `slide` transitions, while horizontal transitions use the X axis.

### Transition Speed

Supported values:

- `fast`
- `default`
- `slow`

```blade
<x-slidewire::slide transition="slide" transition-speed="fast">
    <h2>Fast Slide Transition</h2>
</x-slidewire::slide>
```

## Auto-slide

Automatically advance to next slide after a delay.

```blade
<x-slidewire::slide auto-slide="2000">
    <h2>Auto advance in 2s</h2>
</x-slidewire::slide>
```

You can also define a global default via config (`slides.auto_slide`) or deck-level attribute:

```blade
<x-slidewire::deck auto-slide="3000">
    ...
</x-slidewire::deck>
```

## Auto-animate

Enable morph-style animation between matching elements across consecutive slides.

```blade
<x-slidewire::slide auto-animate="true" auto-animate-duration="700" auto-animate-easing="ease">
    <h2 data-auto-animate-id="title">Before</h2>
    <div data-auto-animate-id="card">A</div>
</x-slidewire::slide>

<x-slidewire::slide auto-animate="true" auto-animate-duration="700" auto-animate-easing="ease">
    <h2 data-auto-animate-id="title">After</h2>
    <div data-auto-animate-id="card">B</div>
</x-slidewire::slide>
```

## Backgrounds

SlideWire supports reveal-like slide backgrounds.

### Color / gradient background (Tailwind classes)

```blade
<x-slidewire::slide class="bg-slate-900 text-white">
    <h2>Solid color</h2>
</x-slidewire::slide>
```

```blade
<x-slidewire::slide class="bg-gradient-to-br from-blue-900 to-slate-950 text-slate-50">
    <h2>Gradient background</h2>
</x-slidewire::slide>
```

### Image background

```blade
<x-slidewire::slide
    background-image="https://example.com/bg.jpg"
    background-size="cover"
    background-position="center"
    background-repeat="no-repeat"
    background-opacity="0.35"
>
    <h2>Image background</h2>
</x-slidewire::slide>
```

### Video background

```blade
<x-slidewire::slide
    background-video="https://example.com/loop.mp4"
    background-video-loop="true"
    background-video-muted="true"
>
    <h2>Video background</h2>
</x-slidewire::slide>
```

### Background transition metadata

```blade
<x-slidewire::slide background-image="https://example.com/bg.jpg" background-transition="fade">
    <h2>Background transition metadata</h2>
</x-slidewire::slide>
```

## Themes

Built-in presets:

- `default`, `black`, `white`, `aurora`, `sunset`, `neon`, `solarized`

Theme presets are configured in `config/slidewire.php` using `ThemeConfig` and `ThemeFont` DTOs.

## Theme-Coupled Code Highlighting

Each theme preset includes a `highlight_theme` key that determines the syntax-highlighting theme for code blocks. This ensures code blocks visually match the active presentation theme automatically.

Override the automatic resolution explicitly:

```blade
<x-slidewire::deck highlight-theme="monokai">
    ...
</x-slidewire::deck>
```

## Font Loading

Custom fonts can be configured in the `fonts` config key. Google Fonts families are loaded automatically via `<link>` tag injection:

```php
'fonts' => [
    'Inter' => ['source' => 'google', 'weights' => [400, 600, 700]],
],
```

System fonts require no loading configuration.

Code blocks use the configured `slides.highlight.font` by default. Override per component when needed:

```blade
<x-slidewire::code language="php" font="FiraCode">
echo 'custom font';
</x-slidewire::code>
```

## Settings Precedence

Runtime settings (theme, transition, auto-slide, etc.) follow a three-level precedence:

1. Slide-level attribute (highest)
2. Deck-level attribute
3. Config default (lowest)

See [Configuration](./configuration.md) for details.

## Fragments

```blade
<x-slidewire::slide>
    <h2>Reveal content gradually</h2>
    <x-slidewire::fragment :index="0"><p>Point A</p></x-slidewire::fragment>
    <x-slidewire::fragment :index="1"><p>Point B</p></x-slidewire::fragment>
</x-slidewire::slide>
```

## Markdown + Highlighting

Use the markdown component inside a slide:

```blade
<x-slidewire::slide class="bg-white text-slate-900">
    <x-slidewire::markdown>
## Metrics

```php
echo 'highlighted';
```
    </x-slidewire::markdown>
</x-slidewire::slide>
```
