<?php

namespace Vleap\Warps\Transformers\Actions;

use Brick\Math\BigInteger;
use MultiversX\Address;
use Vleap\Warps\Actions\TransferAction;

final class TransferActionTransformer
{
    public static function toArray(TransferAction $action): array
    {
        return [
            'type' => $action->getType()->value,
            'label' => $action->label,
            'description' => $action->description,
            'address' => $action->address,
            'data' => $action->data,
            'value' => (string) $action->value,
        ];
    }

    public static function fromArray(array $data): TransferAction
    {
        return new TransferAction(
            label: $data['label'],
            description: $data['description'] ?? null,
            address: $data['address'] ?? null,
            data: $data['data'] ?? null,
            value: BigInteger::of($data['value'] ?? 0),
            inputs: collect($data['inputs'] ?? []),
        );
    }
}
