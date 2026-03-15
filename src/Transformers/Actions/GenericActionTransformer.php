<?php

namespace Vleap\Warps\Transformers\Actions;

use Vleap\Warps\Actions\ActionType;
use Vleap\Warps\Actions\GenericAction;

final class GenericActionTransformer
{
    public static function toArray(GenericAction $action): array
    {
        return $action->getData();
    }

    public static function fromArray(array $data): GenericAction
    {
        $type = ActionType::from($data['type']);

        return new GenericAction(type: $type, data: $data);
    }
}
