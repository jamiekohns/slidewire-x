# Authoring Slides

Each presentation is a single Blade file containing one `<x-slidewire::deck>` with multiple `<x-slidewire::slide>` components.

## File Convention

Example presentation key: `demo/showcase`

File path:

- `resources/views/pages/slides/demo/showcase.blade.php`

## Basic Structure

```blade
<x-slidewire::deck>
    <x-slidewire::slide class="bg-slate-900 text-white">
        <flux:heading size="xl">Kickoff</flux:heading>
        <flux:text>Welcome to SlideWire</flux:text>
    </x-slidewire::slide>

    <x-slidewire::slide class="bg-white text-slate-900">
        <x-slidewire::markdown>
## Markdown Slide

- Use markdown inline
- Keep slide styling in Tailwind classes
        </x-slidewire::markdown>
    </x-slidewire::slide>
</x-slidewire::deck>
```

## Deck-Level Defaults

Set attributes on the deck to apply defaults to all slides:

```blade
<x-slidewire::deck theme="night" transition="fade" auto-slide="3000">
    <x-slidewire::slide>
        <h2>Inherits deck defaults</h2>
    </x-slidewire::slide>

    <x-slidewire::slide theme="white" transition="zoom">
        <h2>Overrides deck defaults</h2>
    </x-slidewire::slide>
</x-slidewire::deck>
```

See [Configuration](./configuration.md) for the full precedence model.

## Vertical Slides

Use `<x-slidewire::vertical-slide>` to group slides vertically within a horizontal column:

```blade
<x-slidewire::deck>
    <x-slidewire::slide>
        <h2>Horizontal Slide 1</h2>
    </x-slidewire::slide>

    <x-slidewire::vertical-slide>
        <x-slidewire::slide>
            <h2>Vertical Top</h2>
        </x-slidewire::slide>
        <x-slidewire::slide>
            <h2>Vertical Bottom</h2>
        </x-slidewire::slide>
    </x-slidewire::vertical-slide>

    <x-slidewire::slide>
        <h2>Horizontal Slide 3</h2>
    </x-slidewire::slide>
</x-slidewire::deck>
```

Navigation:

- **Left/Right arrows** move between horizontal columns.
- **Up/Down arrows** move within a vertical group.
- **Space** advances linearly through all slides (left-to-right, top-to-bottom).

Hash format for vertical slides: `#/slide/{h}/{v}` (1-indexed).

## Components

- `<x-slidewire::deck>`: presentation wrapper, accepts deck-level default attributes
- `<x-slidewire::slide>`: slide container, supports metadata attributes (`transition`, `transition-speed`, `theme`, `auto-slide`, `auto-animate`, background image/video metadata)
- `<x-slidewire::vertical-slide>`: vertical slide group (2D vertical navigation)
- `<x-slidewire::fragment>`: progressive reveal blocks
- `<x-slidewire::markdown>`: markdown + highlighted code output

## Styling

Use Tailwind utility classes for colors, typography, spacing, and layout.

```blade
<x-slidewire::slide class="bg-slate-800 text-slate-50">
    <flux:heading>Theme With Tailwind</flux:heading>
</x-slidewire::slide>
```

Background color should be done with Tailwind classes. Background images and videos stay as slide metadata attributes.
