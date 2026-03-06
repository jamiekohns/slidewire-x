<div align="center">
    <img src="https://github.com/WendellAdriel/slidewire/raw/main/art/logo.png" alt="SlideWire logo" height="220"/>
    <p>
        <h1>SlideWire</h1>
        Create beautiful presentations powered by Livewire
    </p>
</div>

<p align="center">
    <a href="https://packagist.org/packages/wendelladriel/slidewire"><img src="https://img.shields.io/packagist/v/wendelladriel/slidewire.svg?style=flat-square" alt="Packagist"></a>
    <a href="https://packagist.org/packages/wendelladriel/slidewire"><img src="https://img.shields.io/packagist/php-v/wendelladriel/slidewire.svg?style=flat-square" alt="PHP from Packagist"></a>
    <a href="https://packagist.org/packages/wendelladriel/slidewire"><img src="https://img.shields.io/badge/Laravel-12.x,13.x-brightgreen.svg?style=flat-square" alt="Laravel Version"></a>
    <a href="https://github.com/WendellAdriel/slidewire/actions"><img alt="GitHub Workflow Status (main)" src="https://img.shields.io/github/actions/workflow/status/WendellAdriel/slidewire/tests.yml?branch=main&label=Tests"></a>
</p>

SlideWire is a Laravel package for building presentation decks with Livewire, with reveal-style navigation, transitions, theming, fragments, auto-slide support, and PDF export.

## Documentation

- [Installation](./docs/installation.md)
- [Quickstart](./docs/quickstart.md)
- [Routing](./docs/routing.md)
- [Authoring Slides](./docs/authoring.md)
- [Presentation Features](./docs/presentation-features.md)
- [Configuration](./docs/configuration.md)
- [Commands](./docs/commands.md)

## Installation

```bash
composer require wendelladriel/slidewire
```

## Features

- Full-page deck rendering with Livewire
- Keyboard, click, swipe, and hash-based navigation
- Horizontal slides with nested vertical slide groups
- Directional controls, progress, and fullscreen support
- Transition presets, fragments, and auto-animate
- Auto-slide timers with config, deck, and slide precedence
- Syntax highlighting with Phiki and theme-aware configuration
- Reveal-style backgrounds with color, image, and video support
- Structured theme presets with typography controls
- PDF export with `slidewire:pdf`

## Credits

- [Wendell Adriel](https://github.com/WendellAdriel)
- [All Contributors](../../contributors)
