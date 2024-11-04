<?php

namespace Vleap\Actions;

final class LinkAction
{
    public function __construct(
        public readonly string $url,
        public readonly string $label,
    ) {
    }

    public static function create(string $url, string $label): LinkAction
    {
        return new LinkAction($url, $label);
    }
}
