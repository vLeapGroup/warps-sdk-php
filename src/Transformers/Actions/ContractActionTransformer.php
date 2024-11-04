<?php

namespace Vleap\Transformers\Actions;

use Vleap\Actions\ContractAction;

final class ContractActionTransformer
{
    public static function transform(ContractAction $action): array
    {
        return [
            'name' => $action->name,
            'description' => $action->description,
            'type' => 'contract',
            'address' => $action->address->hex(),
            'endpoint' => $action->endpoint,
            'args' => $action->args,
            'gasLimit' => (string) $action->gasLimit,
        ];
    }
}
