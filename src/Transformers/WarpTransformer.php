<?php

namespace Vleap\Transformers;

use Vleap\Action;
use Vleap\Warp;

class WarpTransformer
{
    public static function transform(Warp $warp): array
    {
        return [
            'name' => $warp->name,
            'description' => $warp->description,
            'actions' => $warp->actions
                ->map(fn (Action $action) => $action->toArray())
                ->toArray(),
        ];
    }
}
