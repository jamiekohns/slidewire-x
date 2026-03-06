<?php

declare(strict_types=1);

arch('support types are in the Support namespace')
    ->expect('WendellAdriel\SlideWire\Support')
    ->toBeClasses()
    ->ignoring([
        WendellAdriel\SlideWire\Support\FontSource::class,
        WendellAdriel\SlideWire\Support\SlideTransition::class,
        WendellAdriel\SlideWire\Support\SlideTransitionSpeed::class,
    ]);

arch('support enums are in the Support namespace')
    ->expect([
        WendellAdriel\SlideWire\Support\FontSource::class,
        WendellAdriel\SlideWire\Support\SlideTransition::class,
        WendellAdriel\SlideWire\Support\SlideTransitionSpeed::class,
    ])
    ->toBeEnums();

arch('view components extend Illuminate Component')
    ->expect('WendellAdriel\SlideWire\View\Components')
    ->toExtend(Illuminate\View\Component::class);

arch('commands extend Illuminate Command')
    ->expect('WendellAdriel\SlideWire\Commands')
    ->toExtend(Illuminate\Console\Command::class);

arch('livewire component extends Livewire Component')
    ->expect(WendellAdriel\SlideWire\Livewire\PresentationDeck::class)
    ->toExtend(Livewire\Component::class);

arch('support classes do not depend on view components')
    ->expect('WendellAdriel\SlideWire\Support')
    ->not->toUse('WendellAdriel\SlideWire\View\Components');

arch('support classes do not depend on commands')
    ->expect('WendellAdriel\SlideWire\Support')
    ->not->toUse('WendellAdriel\SlideWire\Commands');

arch('all source files use strict types')
    ->expect('WendellAdriel\SlideWire')
    ->toUseStrictTypes();
