<?php

namespace Vleap\Actions;

use Brick\Math\BigInteger;
use MultiversX\Address;
use Vleap\Transformers\Actions\ActionContractTransformer;

final class ContractAction implements IWarpAction
{
    const DEFAULT_GAS_LIMIT = 10_000_000;

    public function __construct(
        public readonly string $name,
        public readonly ?string $description,
        public readonly Address $address,
        public readonly ?string $endpoint,
        public readonly array $args,
        public readonly BigInteger $gasLimit,
    ) {
        $this->address = Address::zero();
        $this->endpoint = null;
        $this->args = [];
        $this->gasLimit = BigInteger::zero();
    }

    public function toArray(): array
    {
        return ActionContractTransformer::transform($this);
    }
}
