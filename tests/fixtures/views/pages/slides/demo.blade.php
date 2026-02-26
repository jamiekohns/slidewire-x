<x-slidewire::deck>
    <x-slidewire::slide class="bg-slate-900 text-white">
        <h1>Demo Intro</h1>
        <x-slidewire::fragment>
            <p>First fragment</p>
        </x-slidewire::fragment>
    </x-slidewire::slide>

    <x-slidewire::slide class="bg-white text-slate-900">
        <x-slidewire::markdown>
```php
echo 'hello';
```
        </x-slidewire::markdown>
    </x-slidewire::slide>

    <x-slidewire::slide class="bg-slate-800 text-white">
        <h2>Final slide</h2>
        <p>Thanks!</p>
    </x-slidewire::slide>
</x-slidewire::deck>
