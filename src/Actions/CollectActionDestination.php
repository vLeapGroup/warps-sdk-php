<?php

namespace Vleap\Warps\Actions;

final class CollectActionDestination
{
    public function __construct(
        public readonly string $url,
        public readonly string $method,
        public readonly array $headers,
    ) {
    }

    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'method' => $this->method,
            'headers' => $this->headers,
        ];
    }
}
