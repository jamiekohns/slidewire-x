<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Spatie\LaravelPdf\Enums\Orientation;
use Spatie\LaravelPdf\Facades\Pdf;
use Throwable;
use WendellAdriel\SlideWire\Support\PresentationCompiler;

class SlidePdfCommand extends Command
{
    protected $signature = 'slidewire:pdf
        {presentation : The presentation path, e.g. team/q1-kickoff}
        {--output= : Output path for the generated PDF}
        {--format= : PDF paper format (a4, letter, etc)}
        {--orientation= : portrait or landscape}
        {--notes : Include notes in output}';

    protected $description = 'Export a SlideWire presentation to PDF';

    public function handle(PresentationCompiler $compiler): int
    {
        $presentation = trim((string) $this->argument('presentation'), '/');
        $compiled = $compiler->compile($presentation);
        $columns = $compiled['slides'];

        if ($columns === []) {
            $this->error("Presentation [{$presentation}] was not found.");

            return self::FAILURE;
        }

        // Flatten 2D grid to linear slide list for PDF export
        $slides = $compiler->flattenSlides($columns);

        $output = $this->option('output') ?: storage_path('app/' . $presentation . '.pdf');
        File::ensureDirectoryExists(dirname($output));

        $format = (string) ($this->option('format') ?: config('slidewire.pdf.format', 'a4'));
        $orientation = (string) ($this->option('orientation') ?: config('slidewire.pdf.orientation', 'portrait'));
        $includeNotes = (bool) ($this->option('notes') ?: config('slidewire.pdf.include_notes', false));

        try {
            $pdf = Pdf::view('slidewire::pdf.deck', [
                'slides' => $slides,
                'includeNotes' => $includeNotes,
                'presentation' => $presentation,
            ])->format($format);

            if ($orientation === 'landscape') {
                $pdf->orientation(Orientation::Landscape);
            } else {
                $pdf->orientation(Orientation::Portrait);
            }

            $pdf->save($output);
        } catch (Throwable $e) {
            $this->error("Unable to export PDF: {$e->getMessage()}");
            $this->line('<comment>Ensure spatie/laravel-pdf has a configured driver.</comment>');

            return self::FAILURE;
        }

        $this->info("SlideWire PDF exported to [{$output}].");

        return self::SUCCESS;
    }
}
