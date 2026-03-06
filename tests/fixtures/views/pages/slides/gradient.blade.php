<?php

use Livewire\Component;

new class extends Component {
    //
}; ?>

<x-slidewire::deck theme="default">
    <x-slidewire::slide class="bg-gradient-to-br from-blue-900 to-slate-950 text-slate-50">
        <h1>Gradient Slide</h1>
        <p>This slide has a gradient background</p>
    </x-slidewire::slide>

    <x-slidewire::slide class="bg-gradient-to-b from-emerald-950 via-slate-900 to-slate-950 text-white">
        <h2>Three-stop Gradient</h2>
    </x-slidewire::slide>

    <x-slidewire::slide theme="white">
        <h2>White Theme Override</h2>
        <p>This slide uses the white theme</p>
    </x-slidewire::slide>
</x-slidewire::deck>
