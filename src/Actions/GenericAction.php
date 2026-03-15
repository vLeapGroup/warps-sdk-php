<?php

namespace Vleap\Warps\Actions;

/**
 * Passthrough action for types the PHP SDK does not need to execute
 * (e.g. state, mount, unmount). Preserves the raw data so it can be
 * serialised back without loss.
 */
final class GenericAction implements IWarpAction
{
    public function __construct(
        private readonly ActionType $type,
        private readonly array $data,
    ) {}

    public function getType(): ActionType
    {
        return $this->type;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
