<?php

namespace Vleap\Warps\Actions;

use MultiversX\Address;
use Brick\Math\BigInteger;
use Illuminate\Support\Collection;

final class TransferAction implements IWarpAction
{
    public function __construct(
        public readonly string $label,
        public readonly ?string $description,
        public readonly Address $address,
        public readonly ?string $data,
        public readonly ?BigInteger $value,
        /** @var Collection<WarpActionInput> */
        public readonly Collection $inputs = new Collection,
    ) {
    }

    public function getType(): ActionType
    {
        return ActionType::Transfer;
    }
}
