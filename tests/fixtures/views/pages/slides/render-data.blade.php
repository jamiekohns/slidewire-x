<?php

use Livewire\Component;

new class() extends Component
{
    public string $deckTheme = 'black';

    public function render()
    {
        return $this->view([
            'headline' => 'Render Data Intro',
        ]);
    }

    public function with(): array
    {
        return [
            'headline' => 'With Data Override',
            'supportingCopy' => 'Public properties and render data compile correctly.',
        ];
    }
}; ?>

<x-slidewire::deck theme="{{ $deckTheme }}">
    <x-slidewire::slide class="bg-slate-950 text-white">
        <h1>{{ $headline }}</h1>
        <p>{{ $supportingCopy }}</p>
    </x-slidewire::slide>
</x-slidewire::deck>
