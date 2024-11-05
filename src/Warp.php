<?php

namespace Vleap;

use Illuminate\Support\Collection;
use Vleap\Actions\IWarpAction;
use Vleap\Transformers\WarpTransformer;

class Warp
{
    public function __construct(
        public readonly string $name,
        public readonly string $title,
        public readonly ?string $description,
        public readonly string $preview,
        /** @var Collection<IWarpAction> */
        public readonly Collection $actions = new Collection,
    ) {
    }

    public function toArray(): array
    {
        return WarpTransformer::transform($this);
    }
}
