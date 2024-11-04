<?php

namespace Vleap\Actions;

interface IWarpAction
{
    public function getType(): ActionType;

    public function toArray(): array;
}
