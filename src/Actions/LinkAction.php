<?php

namespace Vleap\Actions;

use Illuminate\Support\Collection;

final class LinkAction implements IWarpAction
{
    public function __construct(
        public readonly string $label,
        public readonly ?string $description,
        public readonly string $url,
        /** @var Collection<WarpActionInput> */
        public readonly Collection $inputs = new Collection,
    ) {
    }

    public function getType(): ActionType
    {
        return ActionType::Link;
    }
}
