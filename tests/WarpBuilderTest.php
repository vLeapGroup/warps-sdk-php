<?php

use Vleap\Warps\Warp;
use Vleap\Warps\WarpBuilder;
use Vleap\Warps\Actions\ContractAction;
use Vleap\Warps\Actions\PromptAction;

it('createFromRaw', function () {
    $warpRaw = json_decode(file_get_contents(__DIR__.'/examples/create-token.json'), true);

    $actual = WarpBuilder::createFromRaw($warpRaw);

    expect($actual)->toBeInstanceOf(Warp::class);
    expect($actual->actions->first())->toBeInstanceOf(ContractAction::class);
});

it('create PromptAction from raw', function () {
    $warpRaw = json_decode(file_get_contents(__DIR__.'/examples/prompt-action.json'), true);

    $actual = WarpBuilder::createFromRaw($warpRaw);

    expect($actual)->toBeInstanceOf(Warp::class);
    expect($actual->actions->first())->toBeInstanceOf(PromptAction::class);
    expect($actual->actions->first()->prompt)->toBe('Create a post about {{TOPIC}}');
});
