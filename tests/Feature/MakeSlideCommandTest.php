<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

it('generates a presentation scaffold', function (): void {
    $root = config('slidewire.presentation_roots.0');
    $target = $root . '/team/q1-kickoff.blade.php';

    File::deleteDirectory($root . '/team');

    Artisan::call('make:slidewire', [
        'name' => 'team/q1-kickoff',
        '--title' => 'Q1 Kickoff',
    ]);

    expect(File::exists($target))->toBeTrue()
        ->and(File::get($target))->toContain('Q1 Kickoff');
});
