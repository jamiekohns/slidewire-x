# Testing and Demo

## Package Quality Gates

Run from package root:

```bash
composer lint
composer test
composer test:browser
```

What each command does:

- `composer lint`: Pint + Rector
- `composer test`: lint checks + type coverage + unit/feature tests (browser excluded)
- `composer test:browser`: browser suite (Playwright + Pest Browser)

## Browser Prerequisites

For browser tests, make sure Playwright browser is installed:

```bash
npm install
npx playwright install chromium
```

## Test Coverage

The test suite covers all documented package features:

| Feature | Test Type | Test File(s) |
|---------|-----------|-------------|
| Command rename (`slidewire:pdf`) | Feature | `SlidePdfCommandTest.php` |
| Scaffold command (`make:slidewire`) | Feature | `MakeSlideCommandTest.php` |
| Presentation rendering | Feature | `PresentationDeckComponentTest.php`, `PresentationDeckExpandedTest.php` |
| Controls/progress/fullscreen toggles | Feature | `ControlsToggleTest.php` |
| SlideWire helper + route compatibility | Feature | `SlideWireHelperTest.php` |
| Markdown + highlighting | Feature/Unit | `MarkdownComponentTest.php`, `CodeHighlighterTest.php`, `CodeHighlighterExpandedTest.php` |
| Route macro | Feature | `RouteMacroTest.php` |
| Compiler: transitions/auto-slide | Unit | `PresentationCompilerTransitionAndAutoSlideTest.php` |
| Compiler: backgrounds | Unit | `PresentationCompilerBackgroundTest.php` |
| Compiler: vertical slides | Unit | `PresentationCompilerVerticalSlideTest.php` |
| Compiler: precedence | Unit | `PresentationCompilerPrecedenceTest.php` |
| Compiler: error handling/edge cases | Unit | `PresentationCompilerExpandedTest.php` |
| Highlight theme resolution | Unit | `CodeHighlighterThemeResolutionTest.php` |
| Theme schema structure | Unit | `ThemeSchemaTest.php` |
| Path resolution | Unit | `PresentationPathResolverTest.php`, `PresentationPathResolverExpandedTest.php` |
| Markdown parsing | Unit | `SlideMarkdownParserTest.php`, `SlideMarkdownParserExpandedTest.php` |
| Effective settings resolution | Unit | `EffectiveSettingsResolverTest.php` |
| Theme/typography resolution | Unit | `ThemeResolverTest.php` |
| Config validation | Unit | `ConfigValidatorTest.php` |
| Architecture constraints | Unit | `ArchitectureTest.php` |
| Browser navigation | Browser | `PresentationNavigationBrowserTest.php` |
| Keyboard navigation | Browser | `KeyboardNavigationBrowserTest.php` |
| Auto-slide behavior | Browser | `AutoSlideBrowserTest.php` |

## Validation Flow

For a full validation of all features, run in order:

```bash
composer lint
composer test
composer test:browser
```

All three must pass. Fix any failures and rerun until green.

## Troubleshooting

- If updates do not appear, clear caches:

```bash
php artisan optimize:clear
```

- If browser tests fail locally, verify Playwright install and Chromium binaries.
