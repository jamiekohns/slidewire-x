<?php

declare(strict_types=1);

use WendellAdriel\SlideWire\Support\PresentationPathResolver;

it('throws exception for empty presentation name', function (): void {
    app(PresentationPathResolver::class)->presentationPath('');
})->throws(InvalidArgumentException::class, 'cannot be empty');

it('throws exception for presentation name that normalizes to empty', function (): void {
    app(PresentationPathResolver::class)->presentationPath('....');
})->throws(InvalidArgumentException::class, 'cannot be empty');

it('sanitizes double-dot path traversal attempts', function (): void {
    $resolver = app(PresentationPathResolver::class);

    // These should not resolve to anything outside the roots
    expect($resolver->presentationPath('../../etc/passwd'))->toBeNull();
    expect($resolver->presentationPath('....//etc/passwd'))->toBeNull();
});

it('returns null for non-existent presentations', function (): void {
    $resolver = app(PresentationPathResolver::class);

    expect($resolver->presentationPath('non-existent-deck'))->toBeNull();
});

it('resolves nested presentation paths', function (): void {
    $resolver = app(PresentationPathResolver::class);
    $path = $resolver->presentationPath('team/q1-kickoff');

    expect($path)->toBeString()
        ->and($path)->toEndWith('team/q1-kickoff.blade.php');
});

it('normalizes backslashes to forward slashes', function (): void {
    $resolver = app(PresentationPathResolver::class);

    // Backslash paths should resolve the same as forward slash
    $path = $resolver->absolutePresentationPath('team\\q1-kickoff');

    expect($path)->toContain('team/q1-kickoff.blade.php');
});

it('strips leading and trailing slashes from presentation names', function (): void {
    $resolver = app(PresentationPathResolver::class);

    $path1 = $resolver->absolutePresentationPath('/demo/');
    $path2 = $resolver->absolutePresentationPath('demo');

    expect($path1)->toBe($path2);
});

it('returns presentation directory for existing presentations', function (): void {
    $resolver = app(PresentationPathResolver::class);
    $dir = $resolver->presentationDirectory('demo');

    expect($dir)->toBeString()
        ->and($dir)->toEndWith('slides');
});

it('returns null directory for non-existent presentations', function (): void {
    $resolver = app(PresentationPathResolver::class);

    expect($resolver->presentationDirectory('nonexistent'))->toBeNull();
});

it('returns first configured root', function (): void {
    $resolver = app(PresentationPathResolver::class);

    expect($resolver->firstRoot())->toBeString()
        ->and($resolver->firstRoot())->toContain('slides');
});
