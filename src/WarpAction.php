<?php

namespace Vleap;

use MultiversX\Address;
use Vleap\Actions\ContractAction;
use Vleap\Actions\LinkAction;
use Vleap\Transformers\ActionTransformer;

class WarpAction
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description = null,
    ) {
    }

    public function contract(string|Address $address, ?string $endpoint = null): ContractAction
    {
        $address = $address instanceof Address
            ? $address
            : new Address($address);

        return ContractAction::create($address, $endpoint);
    }

    public function link(string $url, string $label): LinkAction
    {
        return LinkAction::create($url, $label);
    }

    public function toArray(): array
    {
        return ActionTransformer::transform($this);
    }
}
