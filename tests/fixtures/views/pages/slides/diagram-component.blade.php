<?php

use Livewire\Component;

new class extends Component {
    //
}; ?>

<x-slidewire::deck theme="default">
    <x-slidewire::slide class="bg-slate-900 text-white">
        <x-slidewire::diagram>
flowchart LR
    A[Start] --> B[Process]
    B --> C[End]
        </x-slidewire::diagram>
    </x-slidewire::slide>

    <x-slidewire::slide class="bg-slate-900 text-white">
        <x-slidewire::diagram theme="dark">
graph TD
    X --> Y
    Y --> Z
        </x-slidewire::diagram>
    </x-slidewire::slide>
</x-slidewire::deck>
