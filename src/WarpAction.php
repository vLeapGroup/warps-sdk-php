<?php

namespace Vleap\Warps;

use Brick\Math\BigInteger;
use MultiversX\Address;
use Vleap\Warps\Actions\ContractAction;
use Vleap\Warps\Actions\LinkAction;
use Vleap\Warps\Actions\QueryAction;

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

    public function contract(string|Address $address, ?string $endpoint, array $args, BigInteger $value, int|BigInteger $gasLimit = ContractAction::DEFAULT_GAS_LIMIT): ContractAction
    {
        $address = $address instanceof Address
            ? $address
            : Address::newFromBech32($address);

        return new ContractAction($this->name, $this->description, $address, $endpoint, $args, BigInteger::of($value), BigInteger::of($gasLimit));
    }

    public function query(string|Address $address, string $func, array $args, ?string $abi = null): QueryAction
    {
        $address = $address instanceof Address
            ? $address
            : Address::newFromBech32($address);

        return new QueryAction($this->name, $this->description, $address, $func, $args, $abi);
    }

    public function link(string $url): LinkAction
    {
        return new LinkAction($this->name, $this->description, $url);
    }
}
