<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;

it('renders mermaid container with source text', function (): void {
    $html = Blade::render(<<<'BLADE'
<x-slidewire::diagram>
flowchart LR
    A[Start] --> B[End]
</x-slidewire::diagram>
BLADE);

    expect($html)->toContain('<pre')
        ->and($html)->toContain('class="mermaid slidewire-diagram"')
        ->and($html)->toContain('data-slidewire-diagram')
        ->and($html)->toContain('flowchart LR')
        ->and($html)->toContain('A[Start]');
});

it('includes marker attrs for runtime hook', function (): void {
    $html = Blade::render(<<<'BLADE'
<x-slidewire::diagram>
graph TD
    X --> Y
</x-slidewire::diagram>
BLADE);

    expect($html)->toContain('data-slidewire-diagram')
        ->and($html)->toContain('slidewire-diagram');
});

it('preserves mermaid DSL safely', function (): void {
    $html = Blade::render(<<<'BLADE'
<x-slidewire::diagram>
sequenceDiagram
    Alice->>Bob: Hello Bob, how are you?
    Bob-->>Alice: I am good thanks!
</x-slidewire::diagram>
BLADE);

    expect($html)->toContain('sequenceDiagram')
        ->and($html)->toContain('Alice')
        ->and($html)->toContain('Bob');
});

it('applies optional theme attribute as data attribute', function (): void {
    $html = Blade::render(<<<'BLADE'
<x-slidewire::diagram theme="dark">
flowchart LR
    A --> B
</x-slidewire::diagram>
BLADE);

    expect($html)->toContain('data-mermaid-theme="dark"');
});

it('omits mermaid theme data attribute when theme is not set', function (): void {
    $html = Blade::render(<<<'BLADE'
<x-slidewire::diagram>
flowchart LR
    A --> B
</x-slidewire::diagram>
BLADE);

    expect($html)->not->toContain('data-mermaid-theme');
});

it('renders diagram inside a deck and slide context', function (): void {
    $html = Blade::render(<<<'BLADE'
<x-slidewire::deck theme="night">
    <x-slidewire::slide>
        <x-slidewire::diagram>
flowchart LR
    A --> B
        </x-slidewire::diagram>
    </x-slidewire::slide>
</x-slidewire::deck>
BLADE);

    expect($html)->toContain('data-slidewire-diagram')
        ->and($html)->toContain('flowchart LR');
});
