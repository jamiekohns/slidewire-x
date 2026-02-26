<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use WendellAdriel\SlideWire\Support\PresentationPathResolver;

it('resolves presentation directories from configured roots', function (): void {
    $resolver = app(PresentationPathResolver::class);

    expect($resolver->presentationPath('demo'))->toBeString();
    expect(File::exists($resolver->presentationPath('demo')))->toBeTrue();
});

it('builds the expected presentation path', function (): void {
    $resolver = app(PresentationPathResolver::class);

    expect($resolver->absolutePresentationPath('team/q1-kickoff'))
        ->toEndWith('team/q1-kickoff.blade.php');
});
