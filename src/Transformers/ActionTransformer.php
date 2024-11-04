<?php

namespace Vleap\Transformers;

use Vleap\WarpAction;

final class ActionTransformer
{
    public static function transform(WarpAction $action): array
    {
        return [
            'name' => $action->name,
            'description' => $action->description,
        ];
    }
}
