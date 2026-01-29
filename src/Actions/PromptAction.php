<?php

namespace Vleap\Warps\Actions;

use Illuminate\Support\Collection;

final class PromptAction implements IWarpAction
{
    public function __construct(
        public readonly string|array $label,
        public readonly string|array|null $description,
        public readonly string $prompt,
        /** @var Collection<WarpActionInput> */
        public readonly Collection $inputs = new Collection,
    ) {
    }

    public function getType(): ActionType
    {
        return ActionType::Prompt;
    }
}
