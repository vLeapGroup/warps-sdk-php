<?php

namespace Vleap\Actions;

use Vleap\Transformers\Actions\LinkActionTransformer;

final class LinkAction implements IWarpAction
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description,
        public readonly string $url,
    ) {
    }

    public function toArray(): array
    {
        return LinkActionTransformer::transform($this);
    }
}
