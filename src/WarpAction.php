<?php

namespace Vleap;

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

    public function contract(string|Address $address, ?string $endpoint = null): ContractAction
    {
        $address = $address instanceof Address
            ? $address
            : new Address($address);

        return ContractAction::create($this->name, $this->description, $address, $endpoint);
    }

    public function link(string $url): LinkAction
    {
        return LinkAction::create($this->name, $this->description, $url);
    }
}
