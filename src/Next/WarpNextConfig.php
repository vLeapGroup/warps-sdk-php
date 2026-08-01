<?php

namespace Vleap\Warps\Next;

/**
 * The warp protocol `next` value. Accepts a plain identifier string, a list of
 * entries, or a config object with `success`/`error` paths.
 */
final class WarpNextConfig
{
    public function __construct(
        public readonly string|array|null $success = null,
        public readonly string|array|null $error = null,
    ) {}

    public static function fromRaw(mixed $next): ?self
    {
        if (is_string($next)) {
            return new self(success: $next);
        }

        if (is_array($next) && $next !== []) {
            if (array_is_list($next)) {
                return new self(success: $next);
            }

            return new self(
                success: $next['success'] ?? null,
                error: $next['error'] ?? null,
            );
        }

        return null;
    }

    public function toArray(): ?array
    {
        $result = [];

        if ($this->success !== null) {
            $result['success'] = $this->success;
        }

        if ($this->error !== null) {
            $result['error'] = $this->error;
        }

        return $result === [] ? null : $result;
    }
}
