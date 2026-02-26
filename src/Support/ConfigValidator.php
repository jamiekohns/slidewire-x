<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\Support;

use InvalidArgumentException;

/**
 * Validates and normalizes the slidewire configuration to catch
 * misconfigured themes, fonts, and defaults before runtime consumption.
 */
class ConfigValidator
{
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
            if (! is_array($theme)) {
                throw new InvalidArgumentException(
                    "SlideWire theme [{$name}] must be an array with keys: "
                    . implode(', ', ConfigKeys::THEME_REQUIRED_KEYS) . '.'
                );
            }

            foreach (ConfigKeys::THEME_REQUIRED_KEYS as $key) {
                if (! array_key_exists($key, $theme)) {
                    throw new InvalidArgumentException(
                        "SlideWire theme [{$name}] is missing required key [{$key}]."
                    );
                }
            }

            foreach (['title', 'text'] as $typo) {
                if (! is_array($theme[$typo])) {
                    throw new InvalidArgumentException(
                        "SlideWire theme [{$name}] key [{$typo}] must be an array with keys: "
                        . implode(', ', ConfigKeys::TYPOGRAPHY_REQUIRED_KEYS) . '.'
                    );
                }

                foreach (ConfigKeys::TYPOGRAPHY_REQUIRED_KEYS as $key) {
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
            if (! is_array($config)) {
                throw new InvalidArgumentException(
                    "SlideWire font [{$family}] must be an array with at least a 'source' key."
                );
            }

            if (! isset($config['source'])) {
                throw new InvalidArgumentException(
                    "SlideWire font [{$family}] is missing required key [source]."
                );
            }

            $validSources = ['system', 'google'];

            if (! in_array($config['source'], $validSources, true)) {
                throw new InvalidArgumentException(
                    "SlideWire font [{$family}] has invalid source [{$config['source']}]. Valid sources: " . implode(', ', $validSources) . '.'
                );
            }

            if ($config['source'] === 'google' && isset($config['weights']) && ! is_array($config['weights'])) {
                throw new InvalidArgumentException(
                    "SlideWire font [{$family}] weights must be an array of integers."
                );
            }
        }
    }

    /**
     * Validate the defaults configuration.
     *
     * @param  array<string, mixed>  $defaults
     *
     * @throws InvalidArgumentException when a default value is invalid
     */
    public function validateDefaults(array $defaults): void
    {
        $validTransitions = ['slide', 'fade', 'zoom', 'convex', 'concave', 'none'];

        if (isset($defaults['transition']) && ! in_array($defaults['transition'], $validTransitions, true)) {
            throw new InvalidArgumentException(
                "SlideWire default transition [{$defaults['transition']}] is invalid. Valid transitions: " . implode(', ', $validTransitions) . '.'
            );
        }

        $validSpeeds = ['fast', 'default', 'slow'];

        if (isset($defaults['transition_speed']) && ! in_array($defaults['transition_speed'], $validSpeeds, true)) {
            throw new InvalidArgumentException(
                "SlideWire default transition_speed [{$defaults['transition_speed']}] is invalid. Valid speeds: " . implode(', ', $validSpeeds) . '.'
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
        $this->validateThemes(config(ConfigKeys::THEMES, []));
        $this->validateFonts(config(ConfigKeys::FONTS, []));
        $this->validateDefaults(config(ConfigKeys::DEFAULTS, []));
    }
}
