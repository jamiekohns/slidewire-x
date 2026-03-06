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

    $contents = File::get($target);

    expect(File::exists($target))->toBeTrue()
        ->and($contents)->toContain('Q1 Kickoff')
        ->and($contents)->toContain('use Livewire\\Component;')
        ->and($contents)->toContain('new class extends Component');
});
