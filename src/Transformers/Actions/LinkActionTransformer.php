<?php

namespace Vleap\Warps\Transformers\Actions;

use Vleap\Warps\Actions\LinkAction;

final class LinkActionTransformer
{
    public static function toArray(LinkAction $action): array
    {
        return [
            'type' => $action->getType()->value,
            'label' => $action->label,
            'description' => $action->description,
            'url' => $action->url,
        ];
    }

    public static function fromArray(array $data): LinkAction
    {
        return new LinkAction(
            label: $data['label'],
            description: $data['description'] ?? null,
            url: $data['url'],
        );
    }
}
