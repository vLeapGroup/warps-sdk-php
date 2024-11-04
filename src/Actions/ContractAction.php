<?php

namespace Vleap\Actions;

use Brick\Math\BigInteger;
use MultiversX\Address;
use Vleap\Transformers\Actions\ActionContractTransformer;

final class ContractAction implements IWarpAction
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description,
        public Address $address,
        public ?string $endpoint,
        public array $args,
        public BigInteger $gasLimit,
    ) {
        $this->address = Address::zero();
        $this->endpoint = null;
        $this->args = [];
        $this->gasLimit = BigInteger::zero();
    }

    public static function create(string $name, ?string $description, Address $address, ?string $endpoint = null): ContractAction
    {
        return new ContractAction($name, $description, $address, $endpoint, [], BigInteger::zero());
    }

    public function setAddress(string|Address $address): void
    {
        $this->address = $address instanceof Address
            ? $address
            : new Address($address);
    }

    public function setEndpoint(string $endpoint): void
    {
        $this->endpoint = $endpoint;
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

    public function toArray(): array
    {
        return ActionContractTransformer::transform($this);
    }
}
