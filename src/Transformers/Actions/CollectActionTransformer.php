<?php

namespace Vleap\Warps\Transformers\Actions;

use InvalidArgumentException;
use Vleap\Warps\Actions\CollectAction;
use Vleap\Warps\Actions\CollectActionDestinationHttp;
use Vleap\Warps\Next\WarpNextConfig;

final class CollectActionTransformer
{
    public static function toArray(CollectAction $action): array
    {
        $destination = match (true) {
            $action->destination === null => null,
            is_string($action->destination) => $action->destination,
            $action->destination instanceof CollectActionDestinationHttp => $action->destination->toArray(),
        };

        return [
            'type' => $action->getType()->value,
            'label' => $action->label,
            'description' => $action->description,
            'destination' => $destination,
            'next' => $action->next?->toArray(),
        ];
    }

    public static function fromArray(array $data): CollectAction
    {
        return new CollectAction(
            label: $data['label'] ?? throw new InvalidArgumentException('collect action label is required'),
            description: $data['description'] ?? null,
            destination: self::getDestination($data),
            inputs: collect($data['inputs'] ?? []),
            next: WarpNextConfig::fromRaw($data['next'] ?? null),
        );
    }

    private static function getDestination(array $data): string|CollectActionDestinationHttp|null
    {
        if (! isset($data['destination'])) {
            return null;
        }

        if (is_string($data['destination'])) {
            return $data['destination'];
        }

        return new CollectActionDestinationHttp(
            url: $data['destination']['url'] ?? throw new InvalidArgumentException('collect action destination url is required'),
            method: $data['destination']['method'] ?? null,
            headers: $data['destination']['headers'] ?? null,
        );
    }
}
