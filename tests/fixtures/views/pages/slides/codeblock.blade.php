<?php

use Livewire\Component;

new class extends Component {
    //
}; ?>

<x-slidewire::deck theme="black">
    <x-slidewire::slide class="bg-slate-900 text-white">
        <x-slidewire::markdown>
## Code Highlighting

```php
$deck = new PresentationCompiler();
$slides = $deck->compile('demo');
```
        </x-slidewire::markdown>
    </x-slidewire::slide>

    <x-slidewire::slide theme="white" class="bg-white text-slate-900">
        <x-slidewire::markdown>
## Light Theme Code

```php
echo 'light theme';
```
        </x-slidewire::markdown>
    </x-slidewire::slide>

    <x-slidewire::slide class="bg-slate-900 text-white">
        <x-slidewire::markdown>
## Blade Example

```blade
<x-slidewire::deck theme="black">
    <x-slidewire::slide>Hello</x-slidewire::slide>
</x-slidewire::deck>
```
        </x-slidewire::markdown>
    </x-slidewire::slide>
</x-slidewire::deck>
