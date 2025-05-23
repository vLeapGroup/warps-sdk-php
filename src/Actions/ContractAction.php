<?php

namespace Vleap\Warps\Actions;

use Brick\Math\BigInteger;
use Illuminate\Support\Collection;

final class ContractAction implements IWarpAction
{
    const DEFAULT_GAS_LIMIT = 10_000_000;

    public function __construct(
        public readonly string $label,
        public readonly ?string $description,
        public readonly string $address,
        public readonly ?string $func,
        public readonly array $args,
        public readonly ?BigInteger $value,
        public readonly BigInteger $gasLimit,
        /** @var Collection<WarpActionInput> */
        public readonly Collection $inputs = new Collection,
    ) {
    }

    public function getType(): ActionType
    {
        return ActionType::Contract;
    }
}
