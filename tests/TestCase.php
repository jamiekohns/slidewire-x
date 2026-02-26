<?php

declare(strict_types=1);

namespace Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use WendellAdriel\SlideWire\SlideWireServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            \Livewire\LivewireServiceProvider::class,
            SlideWireServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:Q6Vhu88qbwcQe9dse9dR7y6mH0DmI37uRasJkMJQbgg=');
        $app['config']->set('view.paths', [__DIR__ . '/fixtures/views']);
        $app['config']->set('slidewire.presentation_roots', [__DIR__ . '/fixtures/views/pages/slides']);
    }
}
