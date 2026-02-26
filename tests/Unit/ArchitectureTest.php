<?php

declare(strict_types=1);

arch('support classes are in the Support namespace')
    ->expect('WendellAdriel\SlideWire\Support')
    ->toBeClasses();

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

arch('ConfigKeys is a final class with no dependencies')
    ->expect(WendellAdriel\SlideWire\Support\ConfigKeys::class)
    ->toBeFinal()
    ->not->toUse('Illuminate');

arch('all source files use strict types')
    ->expect('WendellAdriel\SlideWire')
    ->toUseStrictTypes();
