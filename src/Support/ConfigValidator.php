<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\Support;

use InvalidArgumentException;

/**
 * Validates and normalizes the slidewire configuration to catch
 * misconfigured themes, fonts, and slide settings before runtime consumption.
 */
class ConfigValidator
{
    /**
     * @var list<string>
     */
    private const array THEME_REQUIRED_KEYS = ['background', 'highlight_theme', 'title', 'text'];

    /**
     * @var list<string>
     */
    private const array TYPOGRAPHY_REQUIRED_KEYS = ['font', 'color', 'size'];

    /**
     * Validate the themes configuration.
     *
     * @param  array<string, mixed>  $themes
     *
     * @throws InvalidArgumentException when a theme entry is malformed
     */
    public function validateThemes(array $themes): void
    {
        foreach ($themes as $name => $theme) {
            if ($theme instanceof ThemeConfig) {
                $this->validateThemeTypography($name, $theme->title, 'title');
                $this->validateThemeTypography($name, $theme->text, 'text');

                continue;
            }

            if (! is_array($theme)) {
                throw new InvalidArgumentException(
                    "SlideWire theme [{$name}] must be an array or ThemeConfig with keys: "
                    . implode(', ', self::THEME_REQUIRED_KEYS) . '.'
                );
            }

            foreach (self::THEME_REQUIRED_KEYS as $key) {
                if (! array_key_exists($key, $theme)) {
                    throw new InvalidArgumentException(
                        "SlideWire theme [{$name}] is missing required key [{$key}]."
                    );
                }
            }

            foreach (['title', 'text'] as $typo) {
                if ($theme[$typo] instanceof ThemeFont) {
                    $this->validateThemeTypography($name, $theme[$typo], $typo);

                    continue;
                }

                if (! is_array($theme[$typo])) {
                    throw new InvalidArgumentException(
                        "SlideWire theme [{$name}] key [{$typo}] must be an array or ThemeFont with keys: "
                        . implode(', ', self::TYPOGRAPHY_REQUIRED_KEYS) . '.'
                    );
                }

                foreach (self::TYPOGRAPHY_REQUIRED_KEYS as $key) {
                    if (! array_key_exists($key, $theme[$typo])) {
                        throw new InvalidArgumentException(
                            "SlideWire theme [{$name}] typography [{$typo}] is missing required key [{$key}]."
                        );
                    }
                }
            }
        }
    }

    /**
     * Validate the fonts configuration.
     *
     * @param  array<string, mixed>  $fonts
     *
     * @throws InvalidArgumentException when a font entry is malformed
     */
    public function validateFonts(array $fonts): void
    {
        foreach ($fonts as $family => $config) {
            if ($config instanceof FontConfig) {
                $this->validateFontConfig($family, $config);

                continue;
            }

            if (! is_array($config)) {
                throw new InvalidArgumentException(
                    "SlideWire font [{$family}] must be an array or FontConfig with at least a 'source' key."
                );
            }

            if (array_key_exists('source', $config) && FontSource::tryFrom((string) $config['source']) === null) {
                $validSources = array_map(static fn (FontSource $source): string => $source->value, FontSource::cases());

                throw new InvalidArgumentException(
                    'SlideWire font [' . $family . '] has invalid source [' . $config['source'] . ']. Valid sources: ' . implode(', ', $validSources) . '.'
                );
            }

            $this->validateFontConfig($family, new FontConfig(
                source: FontSource::tryFrom((string) ($config['source'] ?? '')) ?? FontSource::System,
                weights: is_array($config['weights'] ?? null)
                    ? array_values(array_map(intval(...), $config['weights']))
                    : [],
            ), $config);
        }
    }

    /**
     * Validate the slide configuration.
     *
     * @param  array<string, mixed>  $slides
     *
     * @throws InvalidArgumentException when a slide config value is invalid
     */
    public function validateSlides(array $slides): void
    {
        $validTransitions = ['slide', 'fade', 'zoom', 'convex', 'concave', 'none'];

        if (isset($slides['transition']) && ! in_array($slides['transition'], $validTransitions, true)) {
            throw new InvalidArgumentException(
                "SlideWire slide transition [{$slides['transition']}] is invalid. Valid transitions: " . implode(', ', $validTransitions) . '.'
            );
        }

        $validSpeeds = ['fast', 'default', 'slow'];

        if (isset($slides['transition_speed']) && ! in_array($slides['transition_speed'], $validSpeeds, true)) {
            throw new InvalidArgumentException(
                "SlideWire slide transition_speed [{$slides['transition_speed']}] is invalid. Valid speeds: " . implode(', ', $validSpeeds) . '.'
            );
        }

        $highlightFontSize = $slides['highlight']['font_size'] ?? null;

        if ($highlightFontSize !== null && (! is_string($highlightFontSize) || trim($highlightFontSize) === '')) {
            throw new InvalidArgumentException(
                'SlideWire slides highlight font_size must be a non-empty string.'
            );
        }
    }

    /**
     * Run all validations on the current config.
     *
     * @throws InvalidArgumentException on invalid configuration
     */
    public function validate(): void
    {
        $this->validateThemes(config('slidewire.themes', []));
        $this->validateFonts(config('slidewire.fonts', []));
        $this->validateSlides(config('slidewire.slides', []));
    }

    protected function validateThemeTypography(string $themeName, ThemeFont $font, string $key): void
    {
        foreach (self::TYPOGRAPHY_REQUIRED_KEYS as $requiredKey) {
            if ($font->{$requiredKey} === '') {
                throw new InvalidArgumentException(
                    "SlideWire theme [{$themeName}] typography [{$key}] is missing required key [{$requiredKey}]."
                );
            }
        }
    }

    /**
     * @param  array<string, mixed>|null  $rawConfig
     */
    protected function validateFontConfig(string $family, FontConfig $config, ?array $rawConfig = null): void
    {
        if (($rawConfig !== null && ! array_key_exists('source', $rawConfig)) || (string) $config === '') {
            throw new InvalidArgumentException(
                'SlideWire font [' . $family . '] is missing required key [source].'
            );
        }

        $validSources = array_map(static fn (FontSource $source): string => $source->value, FontSource::cases());

        if (! in_array($config->source->value, $validSources, true)) {
            throw new InvalidArgumentException(
                'SlideWire font [' . $family . '] has invalid source [' . $config->source->value . ']. Valid sources: ' . implode(', ', $validSources) . '.'
            );
        }

        if ($config->source === FontSource::Google && $rawConfig !== null && array_key_exists('weights', $rawConfig) && ! is_array($rawConfig['weights'])) {
            throw new InvalidArgumentException(
                "SlideWire font [{$family}] weights must be an array of integers."
            );
        }
    }
}
