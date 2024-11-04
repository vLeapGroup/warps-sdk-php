<?php

namespace Vleap;

use Vleap\Action;
use MultiversX\Address;
use Illuminate\Support\Collection;
use Vleap\Transformers\WarpTransformer;

class Warp
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description = null,
        /** @var Collection<Action> */
        public readonly Collection $actions = new Collection,
    ) {
    }

    public function newContract(string|Address $address): Warp
    {
        $address = $address instanceof Address
            ? $address
            : Address::newFromBech32($address);

        return new Warp($address);
    }

    public function toArray(): array
    {
        return WarpTransformer::transform($this);
    }
}
