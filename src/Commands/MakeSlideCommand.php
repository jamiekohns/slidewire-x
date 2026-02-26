<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use WendellAdriel\SlideWire\Support\PresentationPathResolver;

class MakeSlideCommand extends Command
{
    protected $signature = 'make:slidewire
        {name? : The presentation path, e.g. team/q1-kickoff}
        {--presentation= : The presentation path override}
        {--title= : The first slide title}
        {--force : Overwrite existing files}';

    protected $description = 'Create a SlideWire presentation scaffold';

    public function handle(PresentationPathResolver $resolver): int
    {
        $presentation = $this->resolvePresentationName();
        $title = $this->option('title') ?: $this->ask('Presentation title', 'SlideWire Presentation');

        $presentationPath = $resolver->absolutePresentationPath($presentation);

        if (File::exists($presentationPath) && ! (bool) $this->option('force')) {
            $this->error("Slide scaffold already exists at [{$presentationPath}]. Use --force to overwrite.");

            return self::FAILURE;
        }

        File::ensureDirectoryExists(dirname($presentationPath));

        $stub = File::get(__DIR__ . '/../../stubs/presentation.stub');
        $contents = str_replace(['{{ title }}', '{{ presentation }}'], [$title, $presentation], $stub);

        File::put($presentationPath, $contents);

        $this->info("Created SlideWire presentation [{$presentation}] at [{$presentationPath}].");

        return self::SUCCESS;
    }

    protected function resolvePresentationName(): string
    {
        $presentation = $this->option('presentation') ?: $this->argument('name');

        if (is_string($presentation) && trim($presentation) !== '') {
            return trim($presentation, '/');
        }

        return trim((string) $this->ask('Presentation path', 'index'), '/');
    }
}
