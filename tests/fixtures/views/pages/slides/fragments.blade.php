<x-slidewire::deck>
    <x-slidewire::slide class="bg-slate-900 text-white">
        <h1>Fragment Slide</h1>
        <x-slidewire::fragment :index="0">
            <p>Fragment Zero</p>
        </x-slidewire::fragment>
        <x-slidewire::fragment :index="1">
            <p>Fragment One</p>
        </x-slidewire::fragment>
        <x-slidewire::fragment :index="2">
            <p>Fragment Two</p>
        </x-slidewire::fragment>
    </x-slidewire::slide>

    <x-slidewire::slide class="bg-slate-800 text-white">
        <h2>After Fragments</h2>
    </x-slidewire::slide>
</x-slidewire::deck>
