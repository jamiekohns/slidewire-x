<?php

use Livewire\Component;

new class extends Component {
    //
}; ?>

<x-slidewire::deck>
    <x-slidewire::slide class="bg-slate-900 text-white">
        <div class="mx-auto max-w-5xl space-y-6">
            <flux:heading size="xl">Q1 Kickoff</flux:heading>
            <flux:text>
                Presentation key: <strong>team/q1-kickoff</strong>
            </flux:text>
            <x-slidewire::fragment>
                <flux:text>Use the arrow keys, click, or swipe to navigate.</flux:text>
            </x-slidewire::fragment>
        </div>
    </x-slidewire::slide>

    <x-slidewire::slide class="bg-white text-slate-900">
        <div class="mx-auto max-w-4xl space-y-6">
            <x-slidewire::markdown>
## Markdown Support

- Write markdown directly in this single presentation file.
- Combine markdown with Tailwind classes in each slide.
            </x-slidewire::markdown>
        </div>
    </x-slidewire::slide>
</x-slidewire::deck>
