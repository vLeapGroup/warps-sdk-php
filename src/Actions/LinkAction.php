<?php

namespace Vleap\Actions;

use Vleap\Transformers\Actions\LinkActionTransformer;

final class LinkAction implements IWarpAction
{
    public function __construct(
        public readonly string $label,
        public readonly ?string $description,
        public readonly string $url,
    ) {
    }

    public function getType(): ActionType
    {
        return ActionType::Link;
    }

    public function toArray(): array
    {
        return LinkActionTransformer::transform($this);
    }
}
