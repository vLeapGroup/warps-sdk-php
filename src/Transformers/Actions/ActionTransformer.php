<?php

namespace Vleap\Transformers\Actions;

use Exception;
use Vleap\Actions\ActionType;
use Vleap\Actions\IWarpAction;
use Vleap\WarpAction;

final class ActionTransformer
{
    public static function toArray(IWarpAction $action): array
    {
        return match ($action->getType()) {
            ActionType::Contract => ContractActionTransformer::toArray($action),
            ActionType::Link => LinkActionTransformer::toArray($action),
            default => throw new Exception("unsupported action type: {$action->getType()->name}"),
        };
    }

    public static function fromArray(array $data): IWarpAction
    {
        return match ($data['type']) {
            ActionType::Contract->value => ContractActionTransformer::fromArray($data),
            ActionType::Query->value => QueryActionTransformer::fromArray($data),
            ActionType::Link->value => LinkActionTransformer::fromArray($data),
            default => throw new Exception("unsupported action type: {$data['type']}"),
        };
    }
}
