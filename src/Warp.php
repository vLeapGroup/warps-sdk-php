<?php

namespace Vleap;

use Illuminate\Support\Collection;
use Vleap\Actions\IWarpAction;
use Vleap\Transformers\WarpTransformer;

class Warp
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description = null,
        /** @var Collection<IWarpAction> */
        public readonly Collection $actions = new Collection,
    ) {
    }

    public static function create(string $title, ?string $description = null): Warp
    {
        return new Warp($title, $description);
    }

    public function addAction(IWarpAction $action): Warp
    {
        $this->actions->push($action);

        return $this;
    }

    public function toArray(): array
    {
        return WarpTransformer::transform($this);
    }
}
