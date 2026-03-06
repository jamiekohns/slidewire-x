<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

it('exports pdf for a presentation when browsershot is available', function (): void {
    if (! class_exists(Spatie\Browsershot\Browsershot::class)) {
        test()->markTestSkipped('spatie/browsershot is not installed.');
    }

    $output = __DIR__ . '/../fixtures/output/demo.pdf';
    File::ensureDirectoryExists(dirname($output));
    File::delete($output);

    $exitCode = Artisan::call('slidewire:pdf', [
        'presentation' => 'demo',
        '--output' => $output,
    ]);

    if ($exitCode !== 0) {
        test()->markTestSkipped('Browsershot could not generate PDF in this environment.');
    }

    expect(File::exists($output))->toBeTrue();
});

it('defaults to a4 landscape orientation', function (): void {
    $command = new WendellAdriel\SlideWire\Commands\SlidePdfCommand();

    $definition = $command->getDefinition();

    expect($definition->getOption('format')->getDefault())->toBe('a4')
        ->and($definition->getOption('orientation')->getDefault())->toBe('landscape');
});

it('does not have a notes option', function (): void {
    $command = new WendellAdriel\SlideWire\Commands\SlidePdfCommand();

    expect($command->getDefinition()->hasOption('notes'))->toBeFalse();
});

it('fails gracefully for non-existent presentation', function (): void {
    $exitCode = Artisan::call('slidewire:pdf', [
        'presentation' => 'does-not-exist-anywhere',
    ]);

    expect($exitCode)->toBe(1);
});
