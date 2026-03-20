<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use WendellAdriel\SlideWire\Commands\MakeSlideCommand;
use WendellAdriel\SlideWire\Contracts\DatabaseDocumentProvider;
use WendellAdriel\SlideWire\Support\CodeBlockPrecompiler;
use WendellAdriel\SlideWire\Support\CodeHighlighter;
use WendellAdriel\SlideWire\Support\ConfigValidator;
use WendellAdriel\SlideWire\Support\EffectiveSettingsResolver;
use WendellAdriel\SlideWire\Support\PresentationCompiler;
use WendellAdriel\SlideWire\Support\PresentationPathResolver;
use WendellAdriel\SlideWire\Support\PresenterSyncStore;
use WendellAdriel\SlideWire\Support\SlideContext;
use WendellAdriel\SlideWire\Support\ThemeResolver;

class SlideWireServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/slidewire.php', 'slidewire');

        $this->app->singleton(PresentationPathResolver::class);
        $this->app->singleton(CodeHighlighter::class);
        $this->app->singleton(PresentationCompiler::class);
        $this->app->singleton(EffectiveSettingsResolver::class);
        $this->app->singleton(PresenterSyncStore::class);
        $this->app->singleton(ThemeResolver::class);
        $this->app->singleton(SlideContext::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'slidewire');

        Blade::componentNamespace('WendellAdriel\\SlideWire\\View\\Components', 'slidewire');
        Blade::prepareStringsForCompilationUsing(new CodeBlockPrecompiler());

        if (! Route::hasMacro('slidewire')) {
            Route::macro('slidewire', function (string $uri, string $presentationOrProvider): \Illuminate\Routing\Route {
                $trimmed = trim($uri, '/');

                if (class_exists($presentationOrProvider) && is_subclass_of($presentationOrProvider, DatabaseDocumentProvider::class)) {
                    $routeUri = $trimmed === '' ? '{document}' : "{$trimmed}/{document}";
                    $routeName = $trimmed === '' ? 'slidewire.database.root' : 'slidewire.database.' . str_replace('/', '.', $trimmed);

                    return Route::livewire($routeUri, 'slidewire::presentation-deck')
                        ->name($routeName)
                        ->defaults('presentation', 'database')
                        ->defaults('documentSource', 'database')
                        ->defaults('documentProvider', $presentationOrProvider);
                }

                return Route::livewire($uri, 'slidewire::presentation-deck')
                    ->name('slidewire.' . str_replace('/', '.', trim($presentationOrProvider, '/')))
                    ->defaults('presentation', trim($presentationOrProvider, '/'));
            });
        }

        Livewire::addNamespace(
            namespace: 'slidewire',
            viewPath: __DIR__ . '/../resources/views/livewire',
            classNamespace: 'WendellAdriel\\SlideWire\\Livewire',
            classPath: __DIR__ . '/Livewire',
            classViewPath: __DIR__ . '/../resources/views/livewire',
        );

        app(ConfigValidator::class)->validate();

        $this->publishes([
            __DIR__ . '/../config/slidewire.php' => config_path('slidewire.php'),
        ], 'slidewire-config');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/slidewire'),
        ], 'slidewire-views');

        $this->publishes([
            __DIR__ . '/../stubs' => base_path('stubs/slidewire'),
        ], 'slidewire-stubs');

        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeSlideCommand::class,
            ]);
        }
    }
}
