<?php

namespace Vleap\Actions;

use Vleap\Transformers\Actions\ActionLinkTransformer;

final class LinkAction implements IWarpAction
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description,
        public string $url,
    ) {
    }

    public static function create(string $name, ?string $description, string $url): LinkAction
    {
        return new LinkAction($name, $description, $url);
    }

    public function setUrl(string $url): LinkAction
    {
        $this->url = $url;

        return $this;
    }

    public function toArray(): array
    {
        return ActionLinkTransformer::transform($this);
    }
}
