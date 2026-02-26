<?php

declare(strict_types=1);

pest()->extend(Tests\TestCase::class)
    ->in('Feature', 'Unit', 'Browser');

expect()->extend('toBeOne', fn () => $this->toBe(1));
