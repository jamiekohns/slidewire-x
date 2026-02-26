<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

it('exports pdf for a presentation when laravel-pdf is installed', function (): void {
    if (! class_exists(Spatie\LaravelPdf\Facades\Pdf::class)) {
        test()->markTestSkipped('spatie/laravel-pdf is not installed.');
    }

    if (! app()->bound(Spatie\LaravelPdf\Drivers\PdfDriver::class)) {
        test()->markTestSkipped('spatie/laravel-pdf driver is not configured in this environment.');
    }

    $output = __DIR__ . '/../fixtures/output/demo.pdf';
    File::ensureDirectoryExists(dirname($output));
    File::delete($output);

    $exitCode = Artisan::call('slidewire:pdf', [
        'presentation' => 'demo',
        '--output' => $output,
    ]);

    expect($exitCode)->toBe(0)
        ->and(File::exists($output))->toBeTrue();
});
