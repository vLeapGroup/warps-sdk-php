<?php

namespace Vleap;

use Vleap\Transformers\ActionTransformer;

class Action
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description = null,
    ) {
    }

    public function toArray(): array
    {
        return ActionTransformer::transform($this);
    }
}
