<?php

use Livewire\Component;

new class extends Component {
    //
}; ?>

<x-slidewire::deck transition="fade">
    <x-slidewire::slide class="bg-slate-900 text-white">
        <h1>Horizontal Slide 1</h1>
    </x-slidewire::slide>

    <x-slidewire::vertical-slide>
        <x-slidewire::slide class="bg-blue-900 text-white">
            <h2>Stack Top</h2>
        </x-slidewire::slide>

        <x-slidewire::slide class="bg-blue-800 text-white">
            <h2>Stack Middle</h2>
        </x-slidewire::slide>

        <x-slidewire::slide class="bg-blue-700 text-white">
            <h2>Stack Bottom</h2>
        </x-slidewire::slide>
    </x-slidewire::vertical-slide>

    <x-slidewire::slide class="bg-slate-800 text-white">
        <h1>Horizontal Slide 3</h1>
    </x-slidewire::slide>
</x-slidewire::deck>
