<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;

it('renders highlighted output with language attribute', function (): void {
    $html = Blade::render(<<<'BLADE'
<x-slidewire::code language="php">
echo 'hello';
</x-slidewire::code>
BLADE);

    expect($html)
        ->toContain('phiki')
        ->toContain('language-php');
});

it('respects explicit theme override on code component', function (): void {
    $html = Blade::render(<<<'BLADE'
<x-slidewire::code language="php" theme="solarized-light">
echo 'hello';
</x-slidewire::code>
BLADE);

    expect($html)->toContain('solarized-light');
});

it('preserves Blade syntax literals inside code component slot', function (): void {
    $html = Blade::render(<<<'BLADE'
<x-slidewire::code language="blade">
<x-slidewire::deck theme="black">
    <x-slidewire::slide>Hello</x-slidewire::slide>
</x-slidewire::deck>
</x-slidewire::code>
BLADE);

    // The original Blade syntax should appear as highlighted text, not compiled
    expect($html)->toContain('phiki')
        ->and($html)->toContain('x-slidewire::deck')
        ->and($html)->toContain('x-slidewire::slide');
});

it('inherits deck and slide theme chain when theme is omitted', function (): void {
    // The 'white' theme maps to 'catppuccin-latte' highlight theme in config
    $html = Blade::render(<<<'BLADE'
<x-slidewire::deck theme="white">
    <x-slidewire::slide>
        <x-slidewire::code language="php">
echo 'hello';
        </x-slidewire::code>
    </x-slidewire::slide>
</x-slidewire::deck>
BLADE);

    expect($html)->toContain('catppuccin-latte');
});

it('inherits slide theme override for highlight resolution', function (): void {
    // Deck uses 'black' (catppuccin-mocha) but slide overrides with 'white' (catppuccin-latte)
    $html = Blade::render(<<<'BLADE'
<x-slidewire::deck theme="black">
    <x-slidewire::slide theme="white">
        <x-slidewire::code language="php">
echo 'hello';
        </x-slidewire::code>
    </x-slidewire::slide>
</x-slidewire::deck>
BLADE);

    expect($html)->toContain('catppuccin-latte');
});

it('uses deck highlight-theme when explicitly set', function (): void {
    $html = Blade::render(<<<'BLADE'
<x-slidewire::deck highlight-theme="solarized-light">
    <x-slidewire::slide>
        <x-slidewire::code language="php">
echo 'hello';
        </x-slidewire::code>
    </x-slidewire::slide>
</x-slidewire::deck>
BLADE);

    expect($html)->toContain('solarized-light');
});

it('uses default highlight theme when no context is set', function (): void {
    $html = Blade::render(<<<'BLADE'
<x-slidewire::code language="php">
echo 'hello';
</x-slidewire::code>
BLADE);

    // Default highlight theme from config is catppuccin-mocha
    expect($html)->toContain('catppuccin-mocha');
});

it('uses configured highlight font by default', function (): void {
    $html = Blade::render(<<<'BLADE'
<x-slidewire::code language="php">
echo 'hello';
</x-slidewire::code>
BLADE);

    expect($html)->toContain('JetBrainsMono');
});

it('uses configured highlight font size class by default', function (): void {
    $html = Blade::render(<<<'BLADE'
<x-slidewire::code language="php">
echo 'hello';
</x-slidewire::code>
BLADE);

    expect($html)->toContain('text-base');
});

it('respects explicit font override on code component', function (): void {
    $html = Blade::render(<<<'BLADE'
<x-slidewire::code language="php" font="FiraCode">
echo 'hello';
</x-slidewire::code>
BLADE);

    expect($html)->toContain('FiraCode')
        ->and($html)->not->toContain('JetBrainsMono');
});

it('respects explicit size override class on code component', function (): void {
    $html = Blade::render(<<<'BLADE'
    <x-slidewire::code language="php" size="text-lg">
echo 'hello';
</x-slidewire::code>
BLADE);

    expect($html)->toContain('text-lg')
        ->and($html)->not->toContain('text-base');
});

it('defaults to text language when language attribute is omitted', function (): void {
    $html = Blade::render(<<<'BLADE'
<x-slidewire::code>
some plain text
</x-slidewire::code>
BLADE);

    // Should render without error, using text language
    expect($html)->not->toBeEmpty();
});

it('preserves HTML tags inside code component from Blade compilation', function (): void {
    $html = Blade::render(<<<'BLADE'
<x-slidewire::code language="html">
<div class="container">
    <h1>Title</h1>
</div>
</x-slidewire::code>
BLADE);

    expect($html)->toContain('phiki')
        ->and($html)->toContain('language-html');
});
