<?php

namespace Vleap\Transformers\Actions;

use Vleap\Actions\LinkAction;

final class LinkActionTransformer
{
    public static function transform(LinkAction $action): array
    {
        return [
            'type' => $action->getType()->value,
            'label' => $action->label,
            'description' => $action->description,
            'url' => $action->url,
        ];
    }
}
