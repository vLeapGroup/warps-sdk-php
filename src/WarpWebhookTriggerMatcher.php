<?php

namespace Vleap\Warps;

class WarpWebhookTriggerMatcher
{
    /**
     * Returns true if the payload satisfies all conditions in the trigger's `match` array.
     * If `match` is absent or empty, always returns true (fires for all events).
     *
     * @param array<string, mixed> $trigger  The warp's trigger definition.
     * @param array<string, mixed> $payload  The incoming webhook payload.
     */
    public static function matches(array $trigger, array $payload): bool
    {
        foreach ($trigger['match'] ?? [] as $dotPath => $expected) {
            if (data_get($payload, $dotPath) !== $expected) {
                return false;
            }
        }

        return true;
    }

    /**
     * Resolves the trigger's `inputs` against the payload.
     * Values containing a dot are treated as dot-paths; others as static literals.
     *
     * @param array<string, mixed> $trigger
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public static function resolveInputs(array $trigger, array $payload): array
    {
        $result = [];

        foreach ($trigger['inputs'] ?? [] as $name => $pathOrLiteral) {
            if (! is_string($pathOrLiteral)) {
                continue;
            }

            $result[$name] = str_contains($pathOrLiteral, '.')
                ? data_get($payload, $pathOrLiteral)
                : $pathOrLiteral;
        }

        return $result;
    }
}
