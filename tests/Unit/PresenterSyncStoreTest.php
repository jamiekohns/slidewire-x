<?php

declare(strict_types=1);

use WendellAdriel\SlideWire\Support\PresenterSyncStore;

it('stores and retrieves presenter sync state', function (): void {
    config()->set('slidewire.presenter_sync', [
        'enabled' => true,
        'poll_interval_ms' => 750,
        'cache_ttl_seconds' => 60,
        'cache_key_prefix' => 'slidewire:test-sync',
    ]);

    $store = app(PresenterSyncStore::class);
    $timestamp = $store->put('database', 42, 3, 1);
    $state = $store->get('database', 42);

    expect($state)->not->toBeNull()
        ->and($state['active_index'])->toBe(3)
        ->and($state['active_fragment'])->toBe(1)
        ->and($state['updated_at_ms'])->toBe($timestamp);
});

it('keeps sync state isolated by document id', function (): void {
    config()->set('slidewire.presenter_sync', [
        'enabled' => true,
        'poll_interval_ms' => 750,
        'cache_ttl_seconds' => 60,
        'cache_key_prefix' => 'slidewire:test-sync',
    ]);

    $store = app(PresenterSyncStore::class);
    $store->put('database', 1, 2, 0);
    $store->put('database', 2, 5, -1);

    $documentOne = $store->get('database', 1);
    $documentTwo = $store->get('database', 2);

    expect($documentOne)->not->toBeNull()
        ->and($documentTwo)->not->toBeNull()
        ->and($documentOne['active_index'])->toBe(2)
        ->and($documentTwo['active_index'])->toBe(5);
});

it('enforces a minimum polling interval', function (): void {
    config()->set('slidewire.presenter_sync', [
        'enabled' => true,
        'poll_interval_ms' => 10,
        'cache_ttl_seconds' => 60,
        'cache_key_prefix' => 'slidewire:test-sync',
    ]);

    $store = app(PresenterSyncStore::class);

    expect($store->pollIntervalMs())->toBe(200);
});
