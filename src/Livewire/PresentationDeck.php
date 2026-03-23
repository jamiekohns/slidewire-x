<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use WendellAdriel\SlideWire\DTOs\Slide;
use WendellAdriel\SlideWire\DTOs\SlidesConfig;
use WendellAdriel\SlideWire\Support\EffectiveSettingsResolver;
use WendellAdriel\SlideWire\Support\PresentationCompiler;
use WendellAdriel\SlideWire\Support\PresenterSyncStore;
use WendellAdriel\SlideWire\Support\SlideViewDataFactory;
use WendellAdriel\SlideWire\Support\ThemeResolver;

#[Layout('slidewire::layouts.blank')]
class PresentationDeck extends Component
{
    #[Locked]
    public string $presentation = 'index';

    /** @var array<int, array<int, Slide>> */
    public array $columns = [];

    /** @var array<int, Slide> */
    public array $slides = [];

    /** @var array<string, string> */
    public array $deckMeta = [];

    /** @var array<int, int> */
    public array $gridShape = [];

    public int $activeIndex = 0;

    public int $activeFragment = -1;

    public ?int $lastPresenterSyncAtMs = null;

    public function mount(
        string $presentation = 'index',
        ?int $startSlide = null,
        ?string $document = null,
        ?string $documentSource = null,
        ?string $documentProvider = null,
    ): void {
        $this->presentation = trim($presentation, '/');
        $compiler = app(PresentationCompiler::class);
        $compiled = $compiler->compile($this->presentation, $document, $documentSource, $documentProvider);
        $this->deckMeta = $compiled['deck_meta'];
        $this->columns = $compiled['slides'];
        $this->slides = $compiler->flattenSlides($this->columns);
        $this->gridShape = array_map(count(...), $this->columns);

        abort_if($this->slides === [], 404, "SlideWire presentation [{$this->presentation}] was not found.");

        if ($startSlide !== null) {
            $this->activeIndex = $this->normalizeIndex($startSlide);
        }

        if ($this->isPresenterController()) {
            $this->publishPresenterState();

            return;
        }

        $this->pollPresenterState();
    }

    public function nextSlide(): void
    {
        if (! $this->canNavigate()) {
            return;
        }

        $fragmentCount = $this->currentSlide()->fragments;

        if ($fragmentCount > 0 && $this->activeFragment < $fragmentCount - 1) {
            ++$this->activeFragment;
            $this->publishPresenterState();

            return;
        }

        $this->activeFragment = -1;
        $this->activeIndex = min($this->activeIndex + 1, count($this->slides) - 1);
        $this->publishPresenterState();
    }

    public function previousSlide(): void
    {
        if (! $this->canNavigate()) {
            return;
        }

        if ($this->activeFragment > -1) {
            --$this->activeFragment;
            $this->publishPresenterState();

            return;
        }

        $previousIndex = max($this->activeIndex - 1, 0);

        if ($previousIndex === $this->activeIndex) {
            return;
        }

        $this->activeIndex = $previousIndex;
        $this->activeFragment = max($this->slides[$this->activeIndex]->fragments - 1, -1);
        $this->publishPresenterState();
    }

    public function goToSlide(int $index, int $fragment = -1): void
    {
        if (! $this->canNavigate()) {
            return;
        }

        $this->activeIndex = $this->normalizeIndex($index);
        $this->activeFragment = $fragment;
        $this->publishPresenterState();
    }

    public function navigateDown(): void
    {
        if (! $this->canNavigate()) {
            return;
        }

        $current = $this->currentSlide();
        $h = $current->h;
        $v = $current->v;
        $maxV = ($this->gridShape[$h] ?? 1) - 1;

        if ($v >= $maxV) {
            return;
        }

        $targetIndex = $this->findFlatIndex($h, $v + 1);

        if ($targetIndex !== null) {
            $this->activeFragment = -1;
            $this->activeIndex = $targetIndex;
            $this->publishPresenterState();
        }
    }

    public function navigateUp(): void
    {
        if (! $this->canNavigate()) {
            return;
        }

        $current = $this->currentSlide();
        $h = $current->h;
        $v = $current->v;

        if ($v <= 0) {
            return;
        }

        $targetIndex = $this->findFlatIndex($h, $v - 1);

        if ($targetIndex !== null) {
            $this->activeFragment = -1;
            $this->activeIndex = $targetIndex;
            $this->publishPresenterState();
        }
    }

    public function pollPresenterState(): void
    {
        if (! $this->shouldFollowPresenter()) {
            return;
        }

        if ((bool) config('slidewire.presenter_sync.enabled', true) === false) {
            return;
        }

        $state = app(PresenterSyncStore::class)->get($this->presentation, $this->documentId());

        if ($state === null) {
            return;
        }

        $updatedAtMs = $state['updated_at_ms'];

        if ($this->lastPresenterSyncAtMs !== null && $updatedAtMs <= $this->lastPresenterSyncAtMs) {
            return;
        }

        $this->activeIndex = $this->normalizeIndex($state['active_index']);
        $this->activeFragment = $state['active_fragment'];
        $this->lastPresenterSyncAtMs = $updatedAtMs;
    }

    public function render(): View
    {
        $settingsResolver = app(EffectiveSettingsResolver::class);
        $themeResolver = app(ThemeResolver::class);
        $viewDataFactory = app(SlideViewDataFactory::class);
        $slidesConfig = config('slidewire.slides', new SlidesConfig());

        $effectiveSlides = $settingsResolver->resolve($this->slides, $this->deckMeta);
        $configuredThemes = $themeResolver->backgroundClassMap();
        $themeTypography = $themeResolver->typographyClassMap();
        $defaultTheme = (string) ($this->deckMeta['theme'] ?? $slidesConfig->theme);
        $canInteract = $this->canNavigate();
        $shouldFollowPresenter = $this->shouldFollowPresenter();
        $presenterSyncPollMs = app(PresenterSyncStore::class)->pollIntervalMs();

        return view('slidewire::livewire.presentation-deck', [
            'slidesConfig' => $slidesConfig,
            'configuredThemes' => $configuredThemes,
            'defaultTheme' => $defaultTheme,
            'deckPayload' => $viewDataFactory->buildDeckPayload($effectiveSlides),
            'slideFrames' => $viewDataFactory->buildSlideFrames($effectiveSlides, $themeTypography, defaultTheme: $defaultTheme),
            'themeTypography' => $themeTypography,
            'googleFontsUrl' => $themeResolver->googleFontsUrl(),
            'codeFontFamily' => $themeResolver->codeFontFamily(),
            'slideThemes' => $themeResolver->slideThemes($effectiveSlides),
            'hasVerticalSlides' => $themeResolver->hasVerticalSlides($this->gridShape),
            'showControls' => $viewDataFactory->resolveDeckFlag($this->deckMeta, 'show_controls', $slidesConfig->showControls),
            'showProgress' => $viewDataFactory->resolveDeckFlag($this->deckMeta, 'show_progress', $slidesConfig->showProgress),
            'showFullscreenButton' => $viewDataFactory->resolveDeckFlag($this->deckMeta, 'show_fullscreen_button', $slidesConfig->showFullscreenButton),
            'canInteract' => $canInteract,
            'shouldFollowPresenter' => $shouldFollowPresenter,
            'presenterSyncPollMs' => $presenterSyncPollMs,
        ]);
    }

    protected function canNavigate(): bool
    {
        if (! $this->isPresenterModeEnabled()) {
            return true;
        }

        if ($this->documentOwnerId() === null) {
            return true;
        }

        return $this->isPresenterController();
    }

    protected function isPresenterModeEnabled(): bool
    {
        $slidesConfig = config('slidewire.slides', new SlidesConfig());

        return $slidesConfig->presenterMode;
    }

    protected function isPresenterController(): bool
    {
        if (! $this->isPresenterModeEnabled()) {
            return false;
        }

        $ownerId = $this->documentOwnerId();
        $userId = $this->currentUserId();

        return $ownerId !== null && $userId !== null && $ownerId === $userId;
    }

    protected function shouldFollowPresenter(): bool
    {
        if (! $this->isPresenterModeEnabled()) {
            return false;
        }

        if ($this->documentOwnerId() === null) {
            return false;
        }

        return ! $this->isPresenterController();
    }

    protected function publishPresenterState(): void
    {
        if (! $this->isPresenterController()) {
            return;
        }

        if ((bool) config('slidewire.presenter_sync.enabled', true) === false) {
            return;
        }

        $this->lastPresenterSyncAtMs = app(PresenterSyncStore::class)->put(
            $this->presentation,
            $this->documentId(),
            $this->activeIndex,
            $this->activeFragment,
        );
    }

    protected function documentOwnerId(): ?int
    {
        if (! isset($this->deckMeta['_slidewire_owner_id'])) {
            return null;
        }

        return (int) $this->deckMeta['_slidewire_owner_id'];
    }

    protected function documentId(): ?int
    {
        if (! isset($this->deckMeta['_slidewire_document_id'])) {
            return null;
        }

        return (int) $this->deckMeta['_slidewire_document_id'];
    }

    protected function currentUserId(): ?int
    {
        $id = Auth::id();

        if (! is_int($id)) {
            return null;
        }

        return $id;
    }

    protected function currentSlide(): Slide
    {
        return $this->slides[$this->activeIndex];
    }

    protected function normalizeIndex(int $index): int
    {
        return max(0, min($index, count($this->slides) - 1));
    }

    protected function findFlatIndex(int $h, int $v): ?int
    {
        return array_find_key($this->slides, fn (Slide $slide): bool => $slide->h === $h && $slide->v === $v);
    }
}
