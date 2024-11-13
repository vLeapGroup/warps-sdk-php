<?php

namespace Vleap\Transformers\Actions;

use Vleap\Actions\LinkAction;

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
            description: $data['description'],
            url: $data['url'],
        );
    }
}
