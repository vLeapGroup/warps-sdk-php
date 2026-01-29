<?php

namespace Vleap\Warps\Transformers\Actions;

use Vleap\Warps\Actions\PromptAction;

final class PromptActionTransformer
{
    public static function toArray(PromptAction $action): array
    {
        return [
            'type' => $action->getType()->value,
            'label' => $action->label,
            'description' => $action->description,
            'prompt' => $action->prompt,
        ];
    }

    public static function fromArray(array $data): PromptAction
    {
        return new PromptAction(
            label: $data['label'],
            description: $data['description'] ?? null,
            prompt: $data['prompt'],
            inputs: collect($data['inputs'] ?? []),
        );
    }
}
