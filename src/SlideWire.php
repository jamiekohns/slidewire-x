<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire;

use Illuminate\Support\Facades\Route;

class SlideWire
{
    public static function route(string $uri, string $presentation): \Illuminate\Routing\Route
    {
        return Route::slidewire($uri, $presentation);
    }
}
