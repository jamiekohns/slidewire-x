<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use WendellAdriel\SlideWire\Support\SlideContext;

it('renders markdown through the markdown component', function (): void {
    $html = Blade::render(<<<'BLADE'
<x-slidewire::markdown>
## Hello

```php
echo 'hi';
```
</x-slidewire::markdown>
BLADE);

    expect($html)
        ->toContain('<h2>Hello</h2>')
        ->toContain('phiki');
});

it('preserves Blade component syntax inside code fences', function (): void {
    $html = Blade::render(<<<'BLADE'
<x-slidewire::markdown>
```blade
<x-slidewire::deck theme="black">
    <x-slidewire::slide>Hello</x-slidewire::slide>
</x-slidewire::deck>
```
</x-slidewire::markdown>
BLADE);

    expect($html)->toContain('phiki')
        ->and($html)->toContain('language-blade')
        ->and($html)->toContain('x-slidewire::deck')
        ->and($html)->toContain('x-slidewire::slide');
});

it('inherits deck theme for highlight theme resolution', function (): void {
    $html = Blade::render(<<<'BLADE'
<x-slidewire::deck theme="white">
    <x-slidewire::slide>
        <x-slidewire::markdown>
```php
echo 'hello';
```
        </x-slidewire::markdown>
    </x-slidewire::slide>
</x-slidewire::deck>
BLADE);

    expect($html)->toContain('catppuccin-latte');
});

it('inherits slide theme override for highlight theme resolution', function (): void {
    $html = Blade::render(<<<'BLADE'
<x-slidewire::deck theme="black">
    <x-slidewire::slide theme="white">
        <x-slidewire::markdown>
```php
echo 'hello';
```
        </x-slidewire::markdown>
    </x-slidewire::slide>
</x-slidewire::deck>
BLADE);

    expect($html)->toContain('catppuccin-latte');
});

it('uses deck highlight-theme when explicitly set', function (): void {
    $html = Blade::render(<<<'BLADE'
<x-slidewire::deck highlight-theme="solarized-light">
    <x-slidewire::slide>
        <x-slidewire::markdown>
```php
echo 'hello';
```
        </x-slidewire::markdown>
    </x-slidewire::slide>
</x-slidewire::deck>
BLADE);

    expect($html)->toContain('solarized-light');
});

it('uses default highlight theme when no deck or slide theme is set', function (): void {
    $html = Blade::render(<<<'BLADE'
<x-slidewire::markdown>
```php
echo 'hello';
```
</x-slidewire::markdown>
BLADE);

    expect($html)->toContain('catppuccin-mocha');
});

it('uses configured highlight font size class for markdown code blocks', function (): void {
    $html = Blade::render(<<<'BLADE'
<x-slidewire::markdown>
```php
echo 'hello';
```
</x-slidewire::markdown>
BLADE);

    expect($html)->toContain('text-base');
});

it('respects explicit size override class on markdown component', function (): void {
    $html = Blade::render(<<<'BLADE'
<x-slidewire::markdown size="text-xl">
```php
echo 'hello';
```
</x-slidewire::markdown>
BLADE);

    expect($html)->toContain('text-xl')
        ->and($html)->not->toContain('text-base');
});

it('clears slide context after slide renders', function (): void {
    $context = app(SlideContext::class);

    Blade::render(<<<'BLADE'
<x-slidewire::deck theme="black">
    <x-slidewire::slide theme="white">
        <p>Content</p>
    </x-slidewire::slide>
</x-slidewire::deck>
BLADE);

    expect($context->presentationTheme())->toBeNull()
        ->and($context->highlightTheme())->toBeNull();
});

it('preserves HTML tags inside code fences from Blade compilation', function (): void {
    $html = Blade::render(<<<'BLADE'
<x-slidewire::markdown>
```html
<div class="container">
    <h1>Title</h1>
    <p>Content</p>
</div>
```
</x-slidewire::markdown>
BLADE);

    expect($html)->toContain('phiki')
        ->and($html)->toContain('language-html');
});
