<?php

use Livewire\Component;

new class extends Component {
    //
}; ?>

<x-slidewire::deck>
    <x-slidewire::slide
        transition="fade"
        transition-speed="slow"
        auto-animate="true"
        auto-animate-duration="600"
        auto-animate-easing="linear"
        class="bg-[url('https://images.example.test/grid.jpg')] bg-contain bg-top bg-repeat text-white"
    >
        <h1>Background Demo</h1>
    </x-slidewire::slide>

    <x-slidewire::slide
        background-video="https://cdn.example.test/loop.mp4"
        background-video-loop="false"
        background-video-muted="true"
        class="bg-slate-800 text-white"
    >
        <h2>Background Utility Classes</h2>
    </x-slidewire::slide>
</x-slidewire::deck>
