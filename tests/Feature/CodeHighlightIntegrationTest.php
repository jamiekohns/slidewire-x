<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

it('renders code blocks with phiki syntax highlighting through the full pipeline', function (): void {
    Route::slidewire('/slides/codeblock', 'codeblock');

    $response = test()->get('/slides/codeblock');
    $content = $response->getContent();

    $response->assertSuccessful();

    // Code should be highlighted with Phiki
    expect($content)->toContain('phiki')
        ->and($content)->toContain('language-php');
});

it('applies night theme highlight (catppuccin-mocha) for deck with theme=night', function (): void {
    Route::slidewire('/slides/codeblock', 'codeblock');

    $response = test()->get('/slides/codeblock');
    $content = $response->getContent();

    // First slide inherits deck theme=night, which maps to catppuccin-mocha
    expect($content)->toContain('catppuccin-mocha');
});

it('applies white theme highlight (catppuccin-latte) for slide with theme=white', function (): void {
    Route::slidewire('/slides/codeblock', 'codeblock');

    $response = test()->get('/slides/codeblock');
    $content = $response->getContent();

    // Second slide overrides with theme=white, which maps to catppuccin-latte
    expect($content)->toContain('catppuccin-latte');
});

it('preserves Blade component syntax in code blocks through compilation', function (): void {
    Route::slidewire('/slides/codeblock', 'codeblock');

    $response = test()->get('/slides/codeblock');
    $content = $response->getContent();

    // Third slide has a blade code block with <x-slidewire::deck> syntax
    // This should appear as highlighted code text, not as compiled HTML
    expect($content)->toContain('language-blade')
        ->and($content)->toContain('x-slidewire::deck')
        ->and($content)->toContain('x-slidewire::slide');
});

it('renders code blocks in demo fixture with phiki highlighting', function (): void {
    Route::slidewire('/slides/demo', 'demo');

    $response = test()->get('/slides/demo');
    $content = $response->getContent();

    $response->assertSuccessful();

    // The demo fixture has a PHP code block
    expect($content)->toContain('phiki')
        ->and($content)->toContain('language-php');
});
