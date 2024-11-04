<?php

namespace Vleap\Transformers;

use Vleap\Action;

class ActionTransformer
{
    public static function transform(Action $action): array
    {
        return [
            'name' => $action->name,
            'description' => $action->description,
        ];
    }
}
