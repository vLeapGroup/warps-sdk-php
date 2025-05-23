<?php

namespace Vleap\Warps;

use MultiversX\Address;
use Brick\Math\BigInteger;
use Illuminate\Support\Collection;
use Vleap\Warps\Actions\LinkAction;
use Vleap\Warps\Actions\QueryAction;
use Vleap\Warps\Actions\CollectAction;
use Vleap\Warps\Actions\ContractAction;
use Vleap\Warps\Actions\TransferAction;
use Vleap\Warps\Actions\CollectActionDestination;

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
            ? $address->bech32()
            : $address;

        return new ContractAction($this->name, $this->description, $address, $endpoint, $args, BigInteger::of($value), BigInteger::of($gasLimit));
    }

    public function query(string|Address $address, string $func, array $args, ?string $abi = null): QueryAction
    {
        $address = $address instanceof Address
            ? $address->bech32()
            : $address;

        return new QueryAction($this->name, $this->description, $address, $func, $args, $abi);
    }

    public function transfer(string|Address $address, ?string $data, BigInteger $value): TransferAction
    {
        $address = $address instanceof Address
            ? $address->bech32()
            : $address;

        return new TransferAction($this->name, $this->description, $address, $data, $value);
    }

    public function collect(CollectActionDestination $destination, Collection $inputs, ?string $next = null): CollectAction
    {
        return new CollectAction($this->name, $this->description, $destination, $inputs, $next);
    }

    public function link(string $url): LinkAction
    {
        return new LinkAction($this->name, $this->description, $url);
    }
}
