<?php

declare(strict_types=1);

namespace WendellAdriel\SlideWire\DTOs;

use Illuminate\Contracts\Support\Arrayable;
use Livewire\Wireable;

/**
 * @phpstan-type SlideMeta array<string, string>
 * @phpstan-type SlideEffective array<string, string|null>
 * @phpstan-type SlideArray array{id: string, html: string, meta: SlideMeta, fragments: int, class: string, h: int, v: int, effective: SlideEffective}
 *
 * @implements Arrayable<string, mixed>
 */
final readonly class Slide implements Arrayable, Wireable
{
    /**
     * @param  SlideMeta  $meta
     * @param  SlideEffective  $effective
     */
    public function __construct(
        public string $id,
        public string $html,
        public array $meta = [],
        public int $fragments = 0,
        public string $class = '',
        public int $h = 0,
        public int $v = 0,
        public array $effective = [],
    ) {}

    /**
     * @param  array{id: string, html: string, meta?: SlideMeta, fragments?: int, class?: string, h?: int, v?: int, effective?: SlideEffective}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            html: $data['html'],
            meta: $data['meta'] ?? [],
            fragments: $data['fragments'] ?? 0,
            class: $data['class'] ?? '',
            h: $data['h'] ?? 0,
            v: $data['v'] ?? 0,
            effective: $data['effective'] ?? [],
        );
    }

    /**
     * @param  array{id: string, html: string, meta?: SlideMeta, fragments?: int, class?: string, h?: int, v?: int, effective?: SlideEffective}  $value
     */
    public static function fromLivewire(mixed $value): self
    {
        if (! is_array($value)) {
            return new self(id: '', html: '');
        }

        return self::fromArray($value);
    }

    public function withCoordinates(int $h, int $v): self
    {
        return new self(
            id: $this->id,
            html: $this->html,
            meta: $this->meta,
            fragments: $this->fragments,
            class: $this->class,
            h: $h,
            v: $v,
            effective: $this->effective,
        );
    }

    /**
     * @param  SlideEffective  $effective
     */
    public function withEffective(array $effective): self
    {
        return new self(
            id: $this->id,
            html: $this->html,
            meta: $this->meta,
            fragments: $this->fragments,
            class: $this->class,
            h: $this->h,
            v: $this->v,
            effective: $effective,
        );
    }

    /**
     * @return SlideArray
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'html' => $this->html,
            'meta' => $this->meta,
            'fragments' => $this->fragments,
            'class' => $this->class,
            'h' => $this->h,
            'v' => $this->v,
            'effective' => $this->effective,
        ];
    }

    /**
     * @return SlideArray
     */
    public function toLivewire(): array
    {
        return $this->toArray();
    }
}
