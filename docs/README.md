# SlideWire Documentation

SlideWire is a Laravel package for creating beautiful presentations powered by Livewire.

## Documentation Index

- [Installation](./installation.md)
- [Quickstart](./quickstart.md)
- [Routing](./routing.md)
- [Authoring Slides](./authoring.md)
- [Presentation Features](./presentation-features.md)
- [Configuration](./configuration.md)
- [Commands](./commands.md)

## What SlideWire Supports

- Full-page deck rendering with Livewire
- Keyboard / click / swipe navigation + hash deep-linking
- 2D navigation: horizontal slides + vertical groups
- Directional arrow controls with up/down support for vertical slides
- Fullscreen mode
- Transition presets and transition speed control (vertical-aware)
- Fragment reveals inside a slide
- Auto-animate between matching elements on consecutive slides
- Auto-slide timers (global default, deck-level, and per-slide override)
- Settings precedence: config -> deck -> slide
- Syntax highlighting with Phiki (theme-coupled via nested `themes` config)
- Reveal-style backgrounds (color, image, video)
- Structured theme presets with typography (title/text font, color, size)
- System-first font loading with Google Fonts fallback
- PDF export (`slidewire:pdf`)
