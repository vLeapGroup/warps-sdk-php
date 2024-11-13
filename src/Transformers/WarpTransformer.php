<?php

namespace Vleap\Transformers;

use Vleap\Actions\IWarpAction;
use Vleap\Transformers\Actions\ActionTransformer;
use Vleap\Warp;

final class WarpTransformer
{
    public static function toArray(Warp $warp): array
    {
        return [
            'protocol' => $warp->protocol,
            'name' => $warp->name,
            'title' => $warp->title,
            'description' => $warp->description,
            'preview' => $warp->preview,
            'actions' => $warp->actions
                ->map(fn (IWarpAction $action) => ActionTransformer::toArray($action))
                ->toArray(),
        ];
    }

    public static function fromArray(array $data): Warp
    {
        return new Warp(
            protocol: $data['protocol'],
            name: $data['name'],
            title: $data['title'],
            description: $data['description'],
            preview: $data['preview'],
            actions: collect($data['actions'])
                ->map(fn (array $action) => ActionTransformer::fromArray($action))
                ->values(),
        );
    }
}
