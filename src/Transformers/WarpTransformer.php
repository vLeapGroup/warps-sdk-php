<?php

namespace Vleap\Warps\Transformers;

use Carbon\Carbon;
use Vleap\Warps\Actions\IWarpAction;
use Vleap\Warps\Transformers\Actions\ActionTransformer;
use Vleap\Warps\Warp;
use Vleap\Warps\WarpMeta;

final class WarpTransformer
{
    public static function toArray(Warp $warp): array
    {
        $result = [
            'protocol' => $warp->protocol,
            'name' => $warp->name,
            'title' => $warp->title,
            'description' => $warp->description,
            'preview' => $warp->preview,
            'actions' => $warp->actions
                ->map(fn (IWarpAction $action) => ActionTransformer::toArray($action))
                ->toArray(),
        ];

        if ($warp->meta !== null) {
            $result['meta'] = self::metaToArray($warp->meta);
        }

        return $result;
    }

    public static function fromArray(array $data): Warp
    {
        return new Warp(
            protocol: $data['protocol'],
            name: $data['name'],
            title: $data['title'],
            description: $data['description'] ?? null,
            preview: $data['preview'] ?? null,
            actions: collect($data['actions'])
                ->map(fn (array $action) => ActionTransformer::fromArray($action))
                ->values(),
            meta: isset($data['meta']) ? self::metaFromArray($data['meta']) : null,
        );
    }

    private static function metaToArray(WarpMeta $meta): array
    {
        return [
            'chain' => $meta->chain,
            'identifier' => $meta->identifier,
            'query' => $meta->query,
            'hash' => $meta->hash,
            'creator' => $meta->creator,
            'createdAt' => $meta->createdAt->toIso8601String(),
        ];
    }

    private static function metaFromArray(array $data): WarpMeta
    {
        return new WarpMeta(
            chain: $data['chain'],
            identifier: $data['identifier'],
            query: $data['query'] ?? null,
            hash: $data['hash'],
            creator: $data['creator'] ?? '',
            createdAt: Carbon::parse($data['createdAt']),
        );
    }
}
