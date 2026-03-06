<x-slidewire::deck theme="black">
    <x-slidewire::slide class="bg-slate-900 text-white">
        <x-slidewire::code language="php">
$deck = new PresentationCompiler();
$slides = $deck->compile('demo');
        </x-slidewire::code>
    </x-slidewire::slide>

    <x-slidewire::slide theme="white" class="bg-white text-slate-900">
        <x-slidewire::code language="php">
echo 'light theme';
        </x-slidewire::code>
    </x-slidewire::slide>

    <x-slidewire::slide class="bg-slate-900 text-white">
        <x-slidewire::code language="blade">
<x-slidewire::deck theme="black">
    <x-slidewire::slide>Hello</x-slidewire::slide>
</x-slidewire::deck>
        </x-slidewire::code>
    </x-slidewire::slide>
</x-slidewire::deck>
