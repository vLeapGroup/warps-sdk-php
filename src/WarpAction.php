<?php

namespace Vleap;

use Brick\Math\BigInteger;
use MultiversX\Address;
use Vleap\Actions\ContractAction;
use Vleap\Actions\LinkAction;

class WarpAction
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description = null,
    ) {
    }

    public static function create(string $name, ?string $description = null): WarpAction
    {
        return new WarpAction($name, $description);
    }

    public function contract(string|Address $address, ?string $endpoint = null, array $args = [], int|BigInteger $gasLimit = ContractAction::DEFAULT_GAS_LIMIT): ContractAction
    {
        $address = $address instanceof Address
            ? $address
            : new Address($address);

        return new ContractAction($this->name, $this->description, $address, $endpoint, $args, BigInteger::of($gasLimit));
    }

    public function link(string $url): LinkAction
    {
        return new LinkAction($this->name, $this->description, $url);
    }
}
