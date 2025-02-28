<?php

namespace Vleap\Warps;

use Illuminate\Support\Collection;

class Warp
{
    public function __construct(
        public readonly string $protocol,
        public readonly string $name,
        public readonly string $title,
        public readonly ?string $description,
        public readonly ?string $preview,
        /** @var Collection<IWarpAction> */
        public readonly Collection $actions = new Collection,
    ) {
    }
}
