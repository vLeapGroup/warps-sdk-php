<?php

use Vleap\Warp;
use Vleap\WarpBuilder;
use Vleap\Actions\ContractAction;

it('createFromRaw', function () {
    $warpRaw = json_decode(file_get_contents(__DIR__.'/examples/create-token.json'), true);

    $actual = WarpBuilder::createFromRaw($warpRaw);

    expect($actual)->toBeInstanceOf(Warp::class);
    expect($actual->actions->first())->toBeInstanceOf(ContractAction::class);
});
