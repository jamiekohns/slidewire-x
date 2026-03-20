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

SlideWire is a Laravel package for building presentation decks with Livewire. Presentations are built as Blade files, rendered as a full-page Livewire experience, and support navigation, themes, fragments, code highlighting, diagrams, vertical stacks, and timed auto-slide flows.

## Documentation

[![Docs Button]][Docs Link] [![DocsRepo Button]][DocsRepo Link]

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

## Database Document Source

SlideWire can compile decks from database markdown documents when the slide source is set to database.

```php
use Illuminate\Support\Facades\Route;
use WendellAdriel\SlideWire\Contracts\DatabaseDocumentProvider;
use WendellAdriel\SlideWire\DTOs\DatabaseDocument;

final class AppDatabaseDocumentProvider implements DatabaseDocumentProvider
{
    public function findById(int $id): ?DatabaseDocument
    {
        $record = \App\Models\Document::query()->find($id);

        if ($record === null) {
            return null;
        }

        return new DatabaseDocument(
            id: $record->id,
            name: $record->name,
            content: $record->markdown,
            ownerId: $record->user_id,
        );
    }
}

Route::slidewire('/presentations', AppDatabaseDocumentProvider::class);
```

The example above registers `presentations/{document}` where `{document}` follows an id-slug format such as `1-First_Test`.
Only the numeric id is used for lookup, and the trailing slug text is treated as a friendly URL label.

When compiling database documents, each `<x-slidewire::slide>...</x-slidewire::slide>` block is interpreted as markdown body content and rendered as if it were authored as:

```blade
<x-slidewire::slide>
    <x-slidewire::markdown>
        ...slide markdown body...
    </x-slidewire::markdown>
</x-slidewire::slide>
```

If the document content does not include an explicit `<x-slidewire::deck>` wrapper, SlideWire wraps the content in one during compilation.

## Credits

- [Wendell Adriel](https://github.com/WendellAdriel)
- [All Contributors](../../contributors)

## Contributing

Check the **[Contributing Guide](CONTRIBUTING.md)**.

<!---------------------------------------------------------------------------->
[Docs Button]: https://img.shields.io/badge/Website-0dB816?style=for-the-badge&logoColor=white&logo=GitBook
[Docs Link]: https://slidewire.dev
[DocsRepo Button]: https://img.shields.io/badge/Repository-3884FF?style=for-the-badge&logoColor=white&logo=GitBook
[DocsRepo Link]: https://github.com/WendellAdriel/slidewire-website
