<?php

namespace Vleap\Transformers;

use Vleap\Actions\IWarpAction;
use Vleap\Warp;

final class WarpTransformer
{
    public static function transform(Warp $warp): array
    {
        return [
            'name' => $warp->name,
            'title' => $warp->title,
            'description' => $warp->description,
            'preview' => $warp->preview,
            'actions' => $warp->actions
                ->map(fn (IWarpAction $action) => $action->toArray())
                ->toArray(),
        ];
    }
}
