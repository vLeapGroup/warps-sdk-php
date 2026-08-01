<?php

namespace Vleap\Warps\Actions;

use MultiversX\Address;
use Illuminate\Support\Collection;
use Vleap\Warps\Next\WarpNextConfig;

final class CollectAction implements IWarpAction
{
    public function __construct(
        public readonly string|array $label,
        public readonly string|array|null $description,
        public readonly string|CollectActionDestinationHttp|null $destination,
        /** @var Collection<WarpActionInput> */
        public readonly Collection $inputs = new Collection,
        public readonly ?WarpNextConfig $next = null,
    ) {
    }

    public function getType(): ActionType
    {
        return ActionType::Collect;
    }
}
