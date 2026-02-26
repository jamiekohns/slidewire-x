<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;

it('renders markdown through the markdown component', function (): void {
    $html = Blade::render(<<<'BLADE'
<x-slidewire::markdown>
## Hello

```php
echo 'hi';
```
</x-slidewire::markdown>
BLADE);

    expect($html)
        ->toContain('<h2>Hello</h2>')
        ->toContain('phiki');
});
