<?php

namespace Vleap\Warps\Transformers\Actions;

use MultiversX\Address;
use Vleap\Warps\Actions\QueryAction;

final class QueryActionTransformer
{
    public static function toArray(QueryAction $action): array
    {
        return [
            'type' => $action->getType()->value,
            'label' => $action->label,
            'description' => $action->description,
            'address' => $action->address,
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
            address: $data['address'],
            func: $data['func'],
            args: $data['args'],
            abi: $data['abi'],
        );
    }
}
