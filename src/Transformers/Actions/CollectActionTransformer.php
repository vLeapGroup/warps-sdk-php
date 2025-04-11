<?php

namespace Vleap\Warps\Transformers\Actions;

use InvalidArgumentException;
use Vleap\Warps\Actions\CollectAction;
use Vleap\Warps\Actions\CollectActionDestination;

final class CollectActionTransformer
{
    public static function toArray(CollectAction $action): array
    {
        return [
            'type' => $action->getType()->value,
            'label' => $action->label,
            'description' => $action->description,
            'destination' => $action->destination->toArray(),
            'next' => $action->next,
        ];
    }

    public static function fromArray(array $data): CollectAction
    {
        return new CollectAction(
            label: $data['label'] ?? throw new InvalidArgumentException('collect action label is required'),
            description: $data['description'] ?? null,
            destination: new CollectActionDestination(
                url: $data['destination']['url'] ?? throw new InvalidArgumentException('collect action destination url is required'),
                method: $data['destination']['method'] ?? throw new InvalidArgumentException('collect action destination method is required'),
                headers: $data['destination']['headers'] ?? [],
            ),
            next: $data['next'] ?? null,
        );
    }
}
