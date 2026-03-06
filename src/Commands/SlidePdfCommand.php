<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Spatie\Browsershot\Browsershot;
use Throwable;
use WendellAdriel\SlideWire\DTOs\SlidesConfig;
use WendellAdriel\SlideWire\Support\EffectiveSettingsResolver;
use WendellAdriel\SlideWire\Support\PresentationCompiler;
use WendellAdriel\SlideWire\Support\SlideViewDataFactory;
use WendellAdriel\SlideWire\Support\ThemeResolver;

class SlidePdfCommand extends Command
{
    protected $signature = 'slidewire:pdf
        {presentation : The presentation path, e.g. team/q1-kickoff}
        {--output= : Output path for the generated PDF}
        {--format=a4 : PDF paper format (a4, letter, etc)}
        {--orientation=landscape : portrait or landscape}';

    protected $description = 'Export a SlideWire presentation to PDF';

    public function handle(PresentationCompiler $compiler, EffectiveSettingsResolver $settingsResolver, ThemeResolver $themeResolver, SlideViewDataFactory $viewDataFactory): int
    {
        $presentation = trim((string) $this->argument('presentation'), '/');
        $compiled = $compiler->compile($presentation);
        $columns = $compiled['slides'];

        if ($columns === []) {
            $this->error("Presentation [{$presentation}] was not found.");

            return self::FAILURE;
        }

        $slides = $compiler->flattenSlides($columns);

        $effectiveSlides = $settingsResolver->resolve($slides, $compiled['deck_meta']);

        $output = $this->option('output') ?: storage_path("app/{$presentation}.pdf");
        File::ensureDirectoryExists(dirname($output));

        $format = (string) $this->option('format');
        $orientation = (string) $this->option('orientation');

        $html = view('slidewire::pdf.deck', [
            'deckMeta' => $compiled['deck_meta'],
            'presentation' => $presentation,
            'slidesConfig' => config('slidewire.slides', new SlidesConfig()),
            'slideFrames' => $viewDataFactory->buildSlideFrames(
                $effectiveSlides,
                $themeResolver->typographyClassMap(),
                $themeResolver->backgroundClassMap(),
                (string) ($compiled['deck_meta']['theme'] ?? config('slidewire.slides', new SlidesConfig())->theme),
            ),
            'googleFontsUrl' => $themeResolver->googleFontsUrl(),
            'codeFontFamily' => $themeResolver->codeFontFamily(),
            'inlineCss' => $this->resolveViteCss(),
        ])->render();

        try {
            $browsershot = Browsershot::html($html)
                ->noSandbox()
                ->format($format)
                ->showBackground()
                ->waitUntilNetworkIdle();

            if ($orientation === 'landscape') {
                $browsershot->landscape();
            }

            $browsershot->savePdf($output);
        } catch (Throwable $e) {
            $this->error("Unable to export PDF: {$e->getMessage()}");
            $this->line('<comment>Ensure Chromium/Chrome and Node.js are available on the system.</comment>');

            return self::FAILURE;
        }

        $this->info("SlideWire PDF exported to [{$output}].");

        return self::SUCCESS;
    }

    /**
     * Read the Vite manifest and inline the CSS content for PDF rendering.
     *
     * Browsershot renders HTML from a temporary file, so relative asset URLs
     * from @vite directives won't resolve. This reads the built CSS directly
     * and returns it as a string for embedding in a <style> tag.
     */
    private function resolveViteCss(): string
    {
        $manifestPath = public_path('build/manifest.json');

        if (! File::exists($manifestPath)) {
            return '';
        }

        $manifest = json_decode(File::get($manifestPath), true);

        if (! is_array($manifest)) {
            return '';
        }

        $css = '';

        foreach ($manifest as $entry) {
            if (! is_array($entry)) {
                continue;
            }

            if (! isset($entry['file'])) {
                continue;
            }

            if (! str_ends_with((string) $entry['file'], '.css')) {
                continue;
            }

            $cssPath = public_path("build/{$entry['file']}");

            if (File::exists($cssPath)) {
                $css .= File::get($cssPath);
            }
        }

        return $css;
    }
}
