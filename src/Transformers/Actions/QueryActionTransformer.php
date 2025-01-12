<?php

namespace Vleap\Transformers\Actions;

use Brick\Math\BigInteger;
use MultiversX\Address;
use Vleap\Actions\QueryAction;

final class QueryActionTransformer
{
    public static function toArray(QueryAction $action): array
    {
        return [
            'type' => $action->getType()->value,
            'label' => $action->label,
            'description' => $action->description,
            'address' => $action->address->bech32(),
            'func' => $action->func,
            'args' => $action->args,
            'abi' => $action->abi,
        ];
    }

    public static function fromArray(array $data): QueryAction
    {
        return new QueryAction(
            label: $data['label'],
            description: $data['description'] ?? null,
            address: Address::newFromBech32($data['address']),
            func: $data['func'],
            args: $data['args'],
            abi: $data['abi'],
        );
    }
}
