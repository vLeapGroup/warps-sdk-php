<?php

namespace Vleap\Transformers;

use Vleap\WarpAction;
use Vleap\Warp;

final class WarpTransformer
{
    public static function transform(Warp $warp): array
    {
        return [
            'name' => $warp->name,
            'description' => $warp->description,
            'actions' => $warp->actions
                ->map(fn (WarpAction $action) => $action->toArray())
                ->toArray(),
        ];
    }
}
