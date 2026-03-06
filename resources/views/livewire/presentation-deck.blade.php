<div
    x-ref="deckRoot"
    x-data="slidewireDeck(
        $wire,
        @entangle('activeIndex').live,
        @entangle('activeFragment').live,
        {{ count($effectiveSlides) }},
        @js($slideThemes),
        @js($configuredThemes),
        @js((string) ($deckMeta['theme'] ?? config('slidewire.slides.theme', 'default'))),
        @js(collect($effectiveSlides)->map(fn ($s) => (int) ($s['effective']['transition_duration'] ?? 350))->values()->all()),
        @js(collect($effectiveSlides)->map(fn ($s) => (int) ($s['effective']['auto_slide'] ?? 0))->values()->all()),
        @js((bool) ($deckMeta['auto_slide_pause_on_interaction'] ?? config('slidewire.slides.auto_slide_pause_on_interaction', true))),
        @js($gridShape),
        @js(collect($effectiveSlides)->map(fn ($s) => ['h' => $s['h'], 'v' => $s['v']])->values()->all()),
        @js($themeTypography)
    )"
    x-bind:class="currentThemeClass()"
    x-on:keydown.window.prevent.right="navigateRight()"
    x-on:keydown.window.prevent.left="navigateLeft()"
    x-on:keydown.window.prevent.down="navigateDown()"
    x-on:keydown.window.prevent.up="navigateUp()"
    x-on:keydown.window.prevent.space="next()"
    class="slidewire-shell"
>
    @if($googleFontsUrl)
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="{{ $googleFontsUrl }}" rel="stylesheet">
    @endif

    <div class="slidewire-stage" x-on:click="next()">
        @if(($deckMeta['show_progress'] ?? config('slidewire.slides.show_progress', true)) !== 'false' && ($deckMeta['show_progress'] ?? config('slidewire.slides.show_progress', true)) !== false)
            <div class="slidewire-progress" role="progressbar" aria-valuemin="0" aria-valuemax="100"
                :aria-valuenow="Math.round(((index + 1) / count) * 100)">
                <div class="slidewire-progress-bar" :style="`width: ${((index + 1) / count) * 100}%`"></div>
            </div>
        @endif

        @foreach ($effectiveSlides as $slideIndex => $slide)
            @php
                $meta = $slide['meta'];
                $effective = $slide['effective'];

                $rawBackground = $meta['background'] ?? null;
                $isBackgroundAsset = is_string($rawBackground) && preg_match('/^(https?:|\/|\.\/|\.\.\/)/', $rawBackground) === 1;

                $backgroundImage = $meta['background_image'] ?? ($isBackgroundAsset ? $rawBackground : null);
                $backgroundVideo = $meta['background_video'] ?? null;

                $frameStyles = [];

                if (is_string($backgroundImage) && $backgroundImage !== '') {
                    $frameStyles[] = 'background-image: url('.$backgroundImage.')';
                    $frameStyles[] = 'background-size: '.($meta['background_size'] ?? 'cover');
                    $frameStyles[] = 'background-position: '.($meta['background_position'] ?? 'center');
                    $frameStyles[] = 'background-repeat: '.($meta['background_repeat'] ?? 'no-repeat');
                }

                if (isset($meta['background_opacity']) && $meta['background_opacity'] !== '') {
                    $frameStyles[] = '--slidewire-background-opacity: '.$meta['background_opacity'];
                }

                $frameStyle = implode(';', $frameStyles);

                $videoLoops = ($meta['background_video_loop'] ?? 'true') !== 'false';
                $videoMuted = ($meta['background_video_muted'] ?? 'true') !== 'false';
            @endphp

            <section
                x-bind:class="frameClass({{ $slideIndex }})"
                x-ref="slide{{ $slideIndex }}"
                wire:key="slide-{{ $slide['id'] }}"
                class="slidewire-frame {{ $slide['class'] }} {{ ($effective['theme'] ?? '') !== '' ? 'slidewire-theme-' . ($effective['theme'] ?? '') : '' }}"
                data-transition="{{ $effective['transition'] ?? config('slidewire.slides.transition') }}"
                data-transition-speed="{{ $effective['transition_speed'] ?? config('slidewire.slides.transition_speed', 'default') }}"
                data-auto-animate="{{ $meta['auto_animate'] ?? $deckMeta['auto_animate'] ?? 'false' }}"
                data-auto-animate-duration="{{ $meta['auto_animate_duration'] ?? $deckMeta['auto_animate_duration'] ?? '420' }}"
                data-auto-animate-easing="{{ $meta['auto_animate_easing'] ?? $deckMeta['auto_animate_easing'] ?? 'ease' }}"
                data-auto-slide="{{ $effective['auto_slide'] ?? '' }}"
                data-theme="{{ $effective['theme'] ?? '' }}"
                data-h="{{ $slide['h'] }}"
                data-v="{{ $slide['v'] }}"
                @if(isset($meta['background_transition']))
                    data-background-transition="{{ $meta['background_transition'] }}"
                @endif
                @if($backgroundImage)
                    data-background-image="{{ $backgroundImage }}"
                @endif
                @if($backgroundVideo)
                    data-background-video="{{ $backgroundVideo }}"
                @endif
                style="{{ $frameStyle }}"
            >
                @if($backgroundVideo)
                    <video class="slidewire-background-media" autoplay playsinline @if($videoMuted) muted @endif @if($videoLoops) loop @endif>
                        <source src="{{ $backgroundVideo }}" />
                    </video>
                @endif

                @php
                    $slideThemeName = $effective['theme'] ?? $deckMeta['theme'] ?? config('slidewire.slides.theme', 'default');
                    $slideTypography = $themeTypography[$slideThemeName] ?? ['title' => '', 'text' => ''];
                @endphp

                <div class="slidewire-content {{ $slideTypography['text'] }}">
                    {!! $slide['html'] !!}
                </div>
            </section>
        @endforeach

        @if(($deckMeta['show_controls'] ?? config('slidewire.slides.show_controls', true)) !== 'false' && ($deckMeta['show_controls'] ?? config('slidewire.slides.show_controls', true)) !== false)
            <nav class="slidewire-controls" aria-label="Slide controls">
                <button type="button" x-on:click.stop="navigateLeft()" aria-label="Previous slide" class="slidewire-control-arrow slidewire-control-left" :disabled="!canGoLeft()">
                    <svg viewBox="0 0 20 20" fill="currentColor" width="18" height="18"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                </button>
                @if($hasVerticalSlides)
                    <button type="button" x-on:click.stop="navigateUp()" aria-label="Slide up" class="slidewire-control-arrow slidewire-control-up" :disabled="!canGoUp()">
                        <svg viewBox="0 0 20 20" fill="currentColor" width="18" height="18"><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
                    </button>
                    <button type="button" x-on:click.stop="navigateDown()" aria-label="Slide down" class="slidewire-control-arrow slidewire-control-down" :disabled="!canGoDown()">
                        <svg viewBox="0 0 20 20" fill="currentColor" width="18" height="18"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </button>
                @endif
                <button type="button" x-on:click.stop="navigateRight()" aria-label="Next slide" class="slidewire-control-arrow slidewire-control-right" :disabled="!canGoRight()">
                    <svg viewBox="0 0 20 20" fill="currentColor" width="18" height="18"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                </button>
                @if(($deckMeta['show_fullscreen_button'] ?? config('slidewire.slides.show_fullscreen_button', true)) !== 'false' && ($deckMeta['show_fullscreen_button'] ?? config('slidewire.slides.show_fullscreen_button', true)) !== false)
                    <button type="button" x-on:click.stop="toggleFullscreen()" x-text="isFullscreen ? 'Exit Fullscreen' : 'Fullscreen'" aria-label="Toggle fullscreen"></button>
                @endif
            </nav>
        @endif
    </div>

    <style>
        .slidewire-shell { width: 100%; height: 100dvh; min-height: 100dvh; overflow: hidden; }
        .slidewire-stage { position: relative; width: 100%; height: 100%; overflow: hidden; }
        .slidewire-frame { position: absolute; inset: 0; display: grid; place-items: center; padding: clamp(1rem, 3vw, 3rem); transform-origin: center center; isolation: isolate; }
        .slidewire-frame.is-idle { opacity: 0; visibility: hidden; pointer-events: none; }
        .slidewire-frame.is-active,
        .slidewire-frame.is-leaving { visibility: visible; }
        .slidewire-frame::before { content: ""; position: absolute; inset: 0; background: rgb(3 8 20 / var(--slidewire-background-opacity, 0)); z-index: -1; }
        .slidewire-background-media { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; border: 0; z-index: -2; }
        .slidewire-content { width: min(1400px, 94vw); }
        .slidewire-controls { position: absolute; left: 50%; bottom: .9rem; transform: translateX(-50%); display: flex; align-items: center; justify-content: center; gap: .5rem; padding: .45rem .7rem; border-radius: 999px; backdrop-filter: blur(8px); background: rgb(2 6 23 / 45%); z-index: 30; }
        .slidewire-controls button { border: 1px solid rgb(148 163 184 / 50%); border-radius: 999px; padding: 0.45rem 0.65rem; background: rgb(15 23 42 / 95%); color: #f8fafc; font-size: .95rem; display: inline-flex; align-items: center; justify-content: center; transition: opacity .15s ease; }
        .slidewire-controls button:disabled { opacity: .35; cursor: not-allowed; }
        .slidewire-controls button svg { display: block; }
        .slidewire-progress { position: absolute; top: 0; left: 0; z-index: 35; height: 6px; width: 100%; background: rgb(15 23 42 / 40%); }
        .slidewire-progress-bar { height: 100%; background: linear-gradient(90deg, #38bdf8, #f8fafc); transition: width .2s ease; }
        .slidewire-fragment { opacity: 0; transform: translateY(10px); transition: opacity .2s ease, transform .2s ease; }
        .slidewire-fragment.slidewire-fragment-visible { opacity: 1; transform: translateY(0); }
        .slidewire-content h1, .slidewire-content h2, .slidewire-content h3,
        .slidewire-content h4, .slidewire-content h5, .slidewire-content h6 { line-height: 1.2; }
        .slidewire-content pre.phiki,
        .slidewire-content pre.slidewire-code { padding: 1.25rem 1.5rem; margin: 1rem 0; border-radius: 0.75rem; overflow-x: auto; font-size: 0.9em; line-height: 1.6; }
        .slidewire-content pre.slidewire-code { background: #24292e; color: #e1e4e8; }
        .slidewire-content pre code, .slidewire-content code { font-family: {{ $codeFontFamily }}; }
        .slidewire-content .slidewire-diagram { background: transparent; opacity: 0; color: transparent; }
        .slidewire-content .slidewire-diagram[data-processed] { opacity: 1; transition: opacity .6s ease-out .1s; }
        .slidewire-content .slidewire-diagram svg { max-width: 100%; height: auto; }
    </style>

    @script
        <script>
            window.slidewireDeck = function ($wire, index, fragment, count, slideThemes, configuredThemes, defaultTheme, slideTransitionDurations, slideAutoSlides, pauseOnInteraction, gridShape, slideCoords, themeTypography) {
            return {
                $wire,
                index,
                fragment,
                count,
                slideThemes,
                configuredThemes,
                defaultTheme,
                slideTransitionDurations,
                slideAutoSlides,
                pauseOnInteraction,
                gridShape,
                slideCoords,
                themeTypography: themeTypography || {},
                touchStartX: null,
                touchStartY: null,
                isFullscreen: false,
                autoAnimateSnapshot: null,
                leavingIndex: null,
                isTransitioning: false,
                transitionTimeout: null,
                autoSlideTimeout: null,
                init() {
                    this.syncFromHash();
                    this.refreshFragments();
                    this.setupAutoSlide();
                    this.renderDiagrams();
                    this.observeDiagrams();

                    window.addEventListener('hashchange', () => this.syncFromHash());
                    document.addEventListener('fullscreenchange', () => {
                        this.isFullscreen = document.fullscreenElement !== null;
                    });

                    this.$el.addEventListener('touchstart', (event) => {
                        this.touchStartX = event.touches[0].clientX;
                        this.touchStartY = event.touches[0].clientY;
                    }, { passive: true });

                    this.$el.addEventListener('touchend', (event) => {
                        if (this.touchStartX === null || this.touchStartY === null) {
                            return;
                        }

                        const dx = event.changedTouches[0].clientX - this.touchStartX;
                        const dy = event.changedTouches[0].clientY - this.touchStartY;

                        if (Math.abs(dx) < 35 && Math.abs(dy) < 35) {
                            return;
                        }

                        // Determine dominant axis
                        if (Math.abs(dx) > Math.abs(dy)) {
                            if (dx < 0) {
                                this.navigateRight();
                            } else {
                                this.navigateLeft();
                            }
                        } else {
                            if (dy < 0) {
                                this.navigateDown();
                            } else {
                                this.navigateUp();
                            }
                        }

                        this.touchStartX = null;
                        this.touchStartY = null;
                    }, { passive: true });

                    this.$watch('index', (value, oldValue) => {
                        this.updateHash();
                        this.playTransition(oldValue, value);
                        this.refreshFragments();
                        this.playAutoAnimate(oldValue, value);
                        this.setupAutoSlide();
                        this.renderDiagrams();
                    });

                    this.$watch('fragment', () => {
                        this.refreshFragments();
                        this.setupAutoSlide();
                    });
                },
                currentCoords() {
                    return this.slideCoords[this.index] || { h: 0, v: 0 };
                },
                findFlatIndex(h, v) {
                    for (let i = 0; i < this.slideCoords.length; i++) {
                        if (this.slideCoords[i].h === h && this.slideCoords[i].v === v) {
                            return i;
                        }
                    }

                    return -1;
                },
                canGoLeft() {
                    const { h } = this.currentCoords();

                    return h > 0;
                },
                canGoRight() {
                    const { h } = this.currentCoords();

                    return h < this.gridShape.length - 1;
                },
                canGoUp() {
                    const { v } = this.currentCoords();

                    return v > 0;
                },
                canGoDown() {
                    const { h, v } = this.currentCoords();
                    const maxV = (this.gridShape[h] || 1) - 1;

                    return v < maxV;
                },
                navigateRight() {
                    this.interruptAutoSlide();
                    const { h } = this.currentCoords();

                    if (h >= this.gridShape.length - 1) {
                        return;
                    }

                    const target = this.findFlatIndex(h + 1, 0);

                    if (target >= 0) {
                        this.captureAutoAnimateSnapshot(target);
                        this.$wire.goToSlide(target);
                    }
                },
                navigateLeft() {
                    this.interruptAutoSlide();
                    const { h } = this.currentCoords();

                    if (h <= 0) {
                        return;
                    }

                    const target = this.findFlatIndex(h - 1, 0);

                    if (target >= 0) {
                        this.captureAutoAnimateSnapshot(target);
                        this.$wire.goToSlide(target);
                    }
                },
                navigateDown() {
                    this.interruptAutoSlide();
                    this.$wire.navigateDown();
                },
                navigateUp() {
                    this.interruptAutoSlide();
                    this.$wire.navigateUp();
                },
                updateHash() {
                    const { h, v } = this.currentCoords();

                    if (v > 0) {
                        window.location.hash = `#/slide/${h + 1}/${v + 1}`;
                    } else {
                        window.location.hash = `#/slide/${h + 1}`;
                    }
                },
                frameClass(slideIndex) {
                    if (slideIndex === this.index) {
                        return 'is-active';
                    }

                    if (slideIndex === this.leavingIndex) {
                        return 'is-leaving';
                    }

                    return 'is-idle';
                },
                currentThemeClass() {
                    const activeTheme = this.slideThemes[this.index] || this.defaultTheme || 'default';
                    const classes = this.configuredThemes[activeTheme] || this.configuredThemes[this.defaultTheme] || '';

                    return classes || '';
                },
                syncFromHash() {
                    const match2d = window.location.hash.match(/#\/slide\/(\d+)\/(\d+)/);

                    if (match2d) {
                        const h = Math.max(0, Number(match2d[1]) - 1);
                        const v = Math.max(0, Number(match2d[2]) - 1);
                        const target = this.findFlatIndex(h, v);

                        if (target >= 0) {
                            this.captureAutoAnimateSnapshot(target);
                            this.$wire.goToSlide(target);
                        }

                        return;
                    }

                    const match1d = window.location.hash.match(/#\/slide\/(\d+)/);

                    if (match1d) {
                        const h = Math.max(0, Number(match1d[1]) - 1);
                        const target = this.findFlatIndex(h, 0);

                        if (target >= 0) {
                            this.captureAutoAnimateSnapshot(target);
                            this.$wire.goToSlide(target);
                        }
                    }
                },
                next() {
                    this.interruptAutoSlide();
                    this.captureAutoAnimateSnapshot(this.index + 1);
                    this.$wire.nextSlide();
                },
                previous() {
                    this.interruptAutoSlide();
                    this.captureAutoAnimateSnapshot(this.index - 1);
                    this.$wire.previousSlide();
                },
                transitionDuration(slide, slideIndex) {
                    const base = Number((this.slideTransitionDurations && this.slideTransitionDurations[slideIndex !== undefined ? slideIndex : this.index]) || 350);
                    const speed = (slide?.dataset.transitionSpeed || 'default').toLowerCase();

                    if (speed === 'fast') {
                        return Math.max(140, Math.round(base * 0.55));
                    }

                    if (speed === 'slow') {
                        return Math.round(base * 1.75);
                    }

                    return base;
                },
                playTransition(oldIndex, newIndex) {
                    if (oldIndex === undefined || oldIndex === null || oldIndex === newIndex) {
                        return;
                    }

                    const fromSlide = this.$refs[`slide${oldIndex}`];
                    const toSlide = this.$refs[`slide${newIndex}`];

                    if (!fromSlide || !toSlide) {
                        this.leavingIndex = null;

                        return;
                    }

                    // When auto-animate is active between slides, skip the regular transition
                    // so the morph animation is the only visual effect.
                    // Don't keep the old slide visible — hide it immediately to prevent
                    // both slides overlapping and causing a flicker.
                    if (this.shouldAutoAnimate(fromSlide, toSlide)) {
                        this.leavingIndex = null;
                        this.isTransitioning = false;

                        return;
                    }

                    const transition = (toSlide.dataset.transition || 'slide').toLowerCase();
                    const direction = newIndex > oldIndex ? 1 : -1;
                    const duration = this.transitionDuration(toSlide, newIndex);
                    const easing = 'cubic-bezier(0.22, 0.61, 0.36, 1)';

                    // Determine if this is a vertical transition
                    const fromCoords = this.slideCoords[oldIndex] || { h: 0, v: 0 };
                    const toCoords = this.slideCoords[newIndex] || { h: 0, v: 0 };
                    const isVertical = fromCoords.h === toCoords.h && fromCoords.v !== toCoords.v;

                    this.leavingIndex = oldIndex;
                    this.isTransitioning = true;

                    if (this.transitionTimeout) {
                        window.clearTimeout(this.transitionTimeout);
                    }

                    if (transition === 'none') {
                        this.transitionTimeout = window.setTimeout(() => {
                            this.leavingIndex = null;
                            this.isTransitioning = false;
                        }, 20);

                        return;
                    }

                    const run = (el, keyframes) => el.animate(keyframes, {
                        duration,
                        easing,
                        fill: 'both',
                    });

                    if (transition === 'fade') {
                        run(fromSlide, [{ opacity: 1 }, { opacity: 0 }]);
                        run(toSlide, [{ opacity: 0 }, { opacity: 1 }]);
                    } else if (transition === 'zoom') {
                        run(fromSlide, [
                            { opacity: 1, transform: 'scale(1)' },
                            { opacity: 0, transform: 'scale(0.88)' },
                        ]);
                        run(toSlide, [
                            { opacity: 0, transform: 'scale(1.12)' },
                            { opacity: 1, transform: 'scale(1)' },
                        ]);
                    } else {
                        // Slide transition: use Y axis for vertical, X axis for horizontal
                        if (isVertical) {
                            const vDir = toCoords.v > fromCoords.v ? 1 : -1;
                            run(fromSlide, [
                                { opacity: 1, transform: 'translateY(0)' },
                                { opacity: 0, transform: `translateY(${vDir * -18}%)` },
                            ]);
                            run(toSlide, [
                                { opacity: 0, transform: `translateY(${vDir * 18}%)` },
                                { opacity: 1, transform: 'translateY(0)' },
                            ]);
                        } else {
                            run(fromSlide, [
                                { opacity: 1, transform: 'translateX(0)' },
                                { opacity: 0, transform: `translateX(${direction * -18}%)` },
                            ]);
                            run(toSlide, [
                                { opacity: 0, transform: `translateX(${direction * 18}%)` },
                                { opacity: 1, transform: 'translateX(0)' },
                            ]);
                        }
                    }

                    this.transitionTimeout = window.setTimeout(() => {
                        this.leavingIndex = null;
                        this.isTransitioning = false;
                    }, duration + 25);
                },
                toggleFullscreen() {
                    if (document.fullscreenElement) {
                        document.exitFullscreen();

                        return;
                    }

                    const target = document.documentElement;

                    if (target.requestFullscreen) {
                        target.requestFullscreen();
                    }
                },
                slideAutoSlideDuration() {
                    const activeSlide = this.$refs[`slide${this.index}`];

                    if (!activeSlide) {
                        return 0;
                    }

                    const fromSlide = Number(activeSlide.dataset.autoSlide || 0);

                    if (fromSlide > 0) {
                        return fromSlide;
                    }

                    return Number((this.slideAutoSlides && this.slideAutoSlides[this.index]) || 0);
                },
                interruptAutoSlide() {
                    if (!this.pauseOnInteraction) {
                        return;
                    }

                    if (this.autoSlideTimeout) {
                        window.clearTimeout(this.autoSlideTimeout);
                        this.autoSlideTimeout = null;
                    }
                },
                setupAutoSlide() {
                    if (this.autoSlideTimeout) {
                        window.clearTimeout(this.autoSlideTimeout);
                        this.autoSlideTimeout = null;
                    }

                    const duration = this.slideAutoSlideDuration();

                    if (duration < 1) {
                        return;
                    }

                    this.autoSlideTimeout = window.setTimeout(() => this.next(), duration);
                },
                shouldAutoAnimate(fromSlide, toSlide) {
                    const fromEnabled = fromSlide?.dataset.autoAnimate === 'true';
                    const toEnabled = toSlide?.dataset.autoAnimate === 'true';

                    return fromEnabled || toEnabled;
                },
                captureAutoAnimateSnapshot(targetIndex) {
                    const fromSlide = this.$refs[`slide${this.index}`];
                    const toSlide = this.$refs[`slide${targetIndex}`];

                    if (!fromSlide || !toSlide || !this.shouldAutoAnimate(fromSlide, toSlide)) {
                        this.autoAnimateSnapshot = null;

                        return;
                    }

                    const nodes = fromSlide.querySelectorAll('[data-auto-animate-id]');
                    const map = new Map();

                    nodes.forEach((node) => {
                        const id = node.getAttribute('data-auto-animate-id');

                        if (!id) {
                            return;
                        }

                        const rect = node.getBoundingClientRect();
                        map.set(id, {
                            left: rect.left,
                            top: rect.top,
                            width: rect.width,
                            height: rect.height,
                            opacity: getComputedStyle(node).opacity,
                        });
                    });

                    this.autoAnimateSnapshot = {
                        targetIndex,
                        duration: Number(toSlide.dataset.autoAnimateDuration || fromSlide.dataset.autoAnimateDuration || 420),
                        easing: toSlide.dataset.autoAnimateEasing || fromSlide.dataset.autoAnimateEasing || 'ease',
                        map,
                    };
                },
                playAutoAnimate(oldValue, value) {
                    const snapshot = this.autoAnimateSnapshot;

                    if (!snapshot || snapshot.targetIndex !== value) {
                        return;
                    }

                    const toSlide = this.$refs[`slide${value}`];

                    if (!toSlide) {
                        this.autoAnimateSnapshot = null;

                        return;
                    }

                    const targetNodes = toSlide.querySelectorAll('[data-auto-animate-id]');

                    // Pre-hide every animated node so they don't flash at final position
                    targetNodes.forEach((node) => {
                        node.style.opacity = '0';
                    });

                    // Use requestAnimationFrame to start animations before the first
                    // browser paint, preventing any flash of elements at final positions.
                    requestAnimationFrame(() => {
                        targetNodes.forEach((node) => {
                            node.style.opacity = '';

                            const id = node.getAttribute('data-auto-animate-id');

                            if (!id) {
                                return;
                            }

                            const source = snapshot.map.get(id);

                            if (!source) {
                                node.animate([
                                    { opacity: 0, transform: 'translateY(12px)' },
                                    { opacity: 1, transform: 'translateY(0)' },
                                ], {
                                    duration: Math.max(220, snapshot.duration * 0.7),
                                    easing: snapshot.easing,
                                    fill: 'both',
                                });

                                return;
                            }

                            const rect = node.getBoundingClientRect();
                            const dx = source.left - rect.left;
                            const dy = source.top - rect.top;
                            const sx = source.width > 0 ? source.width / Math.max(1, rect.width) : 1;
                            const sy = source.height > 0 ? source.height / Math.max(1, rect.height) : 1;

                            node.animate([
                                {
                                    transformOrigin: 'top left',
                                    transform: `translate(${dx}px, ${dy}px) scale(${sx}, ${sy})`,
                                    opacity: source.opacity,
                                },
                                {
                                    transformOrigin: 'top left',
                                    transform: 'translate(0, 0) scale(1, 1)',
                                    opacity: 1,
                                },
                            ], {
                                duration: snapshot.duration,
                                easing: snapshot.easing,
                                fill: 'both',
                            });
                        });

                        this.autoAnimateSnapshot = null;
                    });
                },
                observeDiagrams() {
                    // MutationObserver detects when Livewire morphs diagram nodes,
                    // replacing the Mermaid-rendered SVG with the original source text.
                    // This is the only reliable way to re-render after async DOM morphs.
                    // Debounced to avoid retriggering when Mermaid itself modifies the DOM.
                    let diagramTimer = null;

                    const observer = new MutationObserver(() => {
                        // Check if any diagram node lost its SVG (morph replaced it with text)
                        const stale = this.$el.querySelectorAll('[data-slidewire-diagram]');
                        let needsRender = false;

                        for (const node of stale) {
                            if (!node.querySelector('svg')) {
                                needsRender = true;
                                break;
                            }
                        }

                        if (!needsRender) {
                            return;
                        }

                        // Debounce: Livewire morphs fire many mutations in quick succession.
                        // Wait for the morph to settle before re-rendering.
                        if (diagramTimer) {
                            clearTimeout(diagramTimer);
                        }

                        diagramTimer = setTimeout(() => {
                            diagramTimer = null;
                            this.renderDiagrams();
                        }, 80);
                    });

                    observer.observe(this.$el, {
                        childList: true,
                        subtree: true,
                    });
                },
                renderDiagrams() {
                    const allDiagrams = this.$el.querySelectorAll('[data-slidewire-diagram]');

                    if (allDiagrams.length === 0) {
                        return;
                    }

                    // Detect diagrams that were morphed by Livewire: they have
                    // data-processed (Mermaid's idempotency flag) but no longer
                    // contain an SVG because the DOM morph restored original text.
                    allDiagrams.forEach((node) => {
                        if (node.hasAttribute('data-processed') && !node.querySelector('svg')) {
                            node.removeAttribute('data-processed');
                        }
                    });

                    const pending = Array.from(this.$el.querySelectorAll('[data-slidewire-diagram]:not([data-processed])'));

                    if (pending.length === 0) {
                        return;
                    }

                    // Store original source text so we can restore it if needed
                    pending.forEach((node) => {
                        if (!node.hasAttribute('data-slidewire-diagram-src')) {
                            node.setAttribute('data-slidewire-diagram-src', node.textContent.trim());
                        }
                    });

                    const run = async () => {
                        const batch = Array.from(this.$el.querySelectorAll('[data-slidewire-diagram]:not([data-processed])'));

                        if (batch.length === 0) {
                            return;
                        }

                        // Ensure each node has the original source text for Mermaid to parse
                        batch.forEach((node) => {
                            const src = node.getAttribute('data-slidewire-diagram-src');

                            if (src && !node.querySelector('svg')) {
                                node.textContent = src;
                            }
                        });

                        // Group nodes by their Mermaid theme (data-mermaid-theme attr, default 'dark')
                        const groups = {};

                        batch.forEach((node) => {
                            const theme = node.getAttribute('data-mermaid-theme') || 'dark';

                            if (!groups[theme]) {
                                groups[theme] = [];
                            }

                            groups[theme].push(node);
                        });

                        for (const [theme, nodes] of Object.entries(groups)) {
                            window.mermaid.initialize({ startOnLoad: false, theme: theme });

                            try {
                                await window.mermaid.run({ nodes: nodes });
                            } catch (e) {
                                // Silently handle Mermaid parse errors
                            }
                        }
                    };

                    if (typeof window.mermaid !== 'undefined') {
                        run();

                        return;
                    }

                    if (window._slidewireMermaidLoading) {
                        window._slidewireMermaidCallbacks = window._slidewireMermaidCallbacks || [];
                        window._slidewireMermaidCallbacks.push(run);

                        return;
                    }

                    window._slidewireMermaidLoading = true;
                    window._slidewireMermaidCallbacks = [run];

                    const script = document.createElement('script');
                    script.src = 'https://cdn.jsdelivr.net/npm/mermaid@11/dist/mermaid.min.js';
                    script.onload = () => {
                        window.mermaid.initialize({ startOnLoad: false, theme: 'dark' });
                        const callbacks = window._slidewireMermaidCallbacks || [];
                        window._slidewireMermaidCallbacks = [];

                        callbacks.forEach((cb) => cb());
                    };
                    document.head.appendChild(script);
                },
                refreshFragments() {
                    this.$nextTick(() => {
                        const slide = this.$refs[`slide${this.index}`];

                        if (!slide) {
                            return;
                        }

                        const nodes = slide.querySelectorAll('[data-fragment]');

                        nodes.forEach((node, currentIndex) => {
                            const explicit = node.getAttribute('data-fragment-index');
                            const fragmentIndex = explicit === null ? currentIndex : Number(explicit);

                            node.classList.toggle('slidewire-fragment-visible', fragmentIndex <= this.fragment);
                        });
                    });
                }
            }
        }
        </script>
    @endscript
</div>
