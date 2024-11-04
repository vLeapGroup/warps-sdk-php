<?php

namespace Vleap\Transformers\Actions;

use Vleap\Actions\LinkAction;

final class ActionLinkTransformer
{
    public static function transform(LinkAction $action): array
    {
        return [
            'name' => $action->name,
            'description' => $action->description,
            'type' => 'link',
            'url' => $action->url,
        ];
    }
}
