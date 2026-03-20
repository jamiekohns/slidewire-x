<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\Support;

use Illuminate\Support\Facades\Cache;

class PresenterSyncStore
{
    /**
     * @return array{active_index: int, active_fragment: int, updated_at_ms: int}|null
     */
    public function get(string $presentation, ?int $documentId): ?array
    {
        $state = Cache::get($this->key($presentation, $documentId));

        if (! is_array($state)) {
            return null;
        }

        if (! isset($state['active_index'], $state['active_fragment'], $state['updated_at_ms'])) {
            return null;
        }

        return [
            'active_index' => (int) $state['active_index'],
            'active_fragment' => (int) $state['active_fragment'],
            'updated_at_ms' => (int) $state['updated_at_ms'],
        ];
    }

    public function put(string $presentation, ?int $documentId, int $activeIndex, int $activeFragment): int
    {
        $updatedAtMs = (int) floor(microtime(true) * 1000);

        Cache::put(
            $this->key($presentation, $documentId),
            [
                'active_index' => $activeIndex,
                'active_fragment' => $activeFragment,
                'updated_at_ms' => $updatedAtMs,
            ],
            now()->addSeconds($this->cacheTtlSeconds()),
        );

        return $updatedAtMs;
    }

    public function pollIntervalMs(): int
    {
        $value = (int) config('slidewire.presenter_sync.poll_interval_ms', 900);

        return max(200, $value);
    }

    protected function cacheTtlSeconds(): int
    {
        $value = (int) config('slidewire.presenter_sync.cache_ttl_seconds', 1200);

        return max(5, $value);
    }

    protected function key(string $presentation, ?int $documentId): string
    {
        $prefix = (string) config('slidewire.presenter_sync.cache_key_prefix', 'slidewire:presenter-sync');
        $documentKey = $documentId === null ? 'none' : (string) $documentId;

        return "{$prefix}:{$presentation}:{$documentKey}";
    }
}
