<?php

namespace Vleap\Warps\Actions;

use MultiversX\Address;
use Illuminate\Support\Collection;

final class CollectAction implements IWarpAction
{
    public function __construct(
        public readonly string $label,
        public readonly ?string $description,
        public readonly CollectActionDestination $destination,
        /** @var Collection<WarpActionInput> */
        public readonly Collection $inputs = new Collection,
        public readonly ?string $next,
    ) {
    }

    public function getType(): ActionType
    {
        return ActionType::Collect;
    }
}
