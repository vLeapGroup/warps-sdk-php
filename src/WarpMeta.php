<?php

namespace Vleap\Warps;

use Carbon\Carbon;

final class WarpMeta
{
    public function __construct(
        public readonly string $chain,
        public readonly string $identifier,
        public readonly ?string $query,
        public readonly string $hash,
        public readonly string $creator,
        public readonly Carbon $createdAt,
    ) {
    }
}
