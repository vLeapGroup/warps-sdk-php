<?php

namespace Vleap\Warps\Actions;

use Illuminate\Support\Collection;

final class LinkAction implements IWarpAction
{
    public function __construct(
        public readonly string|array $label,
        public readonly string|array|null $description,
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
