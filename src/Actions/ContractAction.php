<?php

namespace Vleap\Actions;

use Brick\Math\BigInteger;
use MultiversX\Address;

final class ContractAction
{
    public function __construct(
        public readonly Address $address,
        public readonly ?string $endpoint = null,
        public array $args,
        public BigInteger $gasLimit,
    ) {
        $this->gasLimit = BigInteger::zero();
    }

    public static function create(Address $address, ?string $endpoint = null): ContractAction
    {
        return new ContractAction($address, $endpoint, [], BigInteger::zero());
    }

    public function setArgs(array $args): ContractAction
    {
        $this->args = $args;

        return $this;
    }

    public function setGasLimit(int|BigInteger $gasLimit): void
    {
        $this->gasLimit = $gasLimit instanceof BigInteger
            ? $gasLimit
            : BigInteger::of($gasLimit);
    }
}
