<?php

use Vleap\Warps\Next\WarpNextConfig;

it('parses a plain string next', function () {
    $config = WarpNextConfig::fromRaw('joai-loyalty-signup-email?name={{name}}');

    expect($config->success)->toBe('joai-loyalty-signup-email?name={{name}}');
    expect($config->error)->toBeNull();
});

it('parses a list of next entries', function () {
    $config = WarpNextConfig::fromRaw(['a', 'b']);

    expect($config->success)->toBe(['a', 'b']);
});

it('parses a config object with success and error', function () {
    $config = WarpNextConfig::fromRaw(['success' => 'on-ok', 'error' => 'on-fail']);

    expect($config->success)->toBe('on-ok');
    expect($config->error)->toBe('on-fail');
});

it('returns null for an empty or missing next', function () {
    expect(WarpNextConfig::fromRaw(null))->toBeNull();
    expect(WarpNextConfig::fromRaw([]))->toBeNull();
});

it('serializes back to a config array', function () {
    $config = WarpNextConfig::fromRaw(['success' => 'on-ok', 'error' => 'on-fail']);

    expect($config->toArray())->toBe(['success' => 'on-ok', 'error' => 'on-fail']);
});

it('round-trips a plain string next', function () {
    $config = WarpNextConfig::fromRaw('next-action');

    expect(WarpNextConfig::fromRaw($config->toArray())->success)->toBe('next-action');
});
