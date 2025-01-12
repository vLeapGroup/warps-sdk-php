<?php

namespace Vleap\Actions;

use MultiversX\Address;
use Illuminate\Support\Collection;

final class QueryAction implements IWarpAction
{
    public function __construct(
        public readonly string $label,
        public readonly ?string $description,
        public readonly Address $address,
        public readonly string $func,
        public readonly array $args,
        public readonly ?string $abi,
        /** @var Collection<WarpActionInput> */
        public readonly Collection $inputs = new Collection,
    ) {
    }

    public function getType(): ActionType
    {
        return ActionType::Query;
    }
}
