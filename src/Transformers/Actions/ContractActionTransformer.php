<?php

namespace Vleap\Warps\Transformers\Actions;

use Brick\Math\BigInteger;
use MultiversX\Address;
use Vleap\Warps\Actions\ContractAction;

final class ContractActionTransformer
{
    public static function toArray(ContractAction $action): array
    {
        return [
            'type' => $action->getType()->value,
            'label' => $action->label,
            'description' => $action->description,
            'address' => $action->address,
            'func' => $action->func,
            'args' => $action->args,
            'value' => (string) $action->value,
            'gasLimit' => (string) $action->gasLimit,
        ];
    }

    public static function fromArray(array $data): ContractAction
    {
        return new ContractAction(
            label: $data['label'],
            description: $data['description'] ?? null,
            address: $data['address'],
            func: $data['func'],
            args: $data['args'],
            gasLimit: BigInteger::of($data['gasLimit'] ?? 0),
            value: BigInteger::of($data['value'] ?? 0),
            inputs: collect($data['inputs'] ?? []),
        );
    }
}
